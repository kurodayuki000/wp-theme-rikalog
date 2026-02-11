import * as esbuild from 'esbuild';
import * as path from 'path';
import * as fs from 'fs';

const isProd = process.argv.includes('--prod');
const isWatch = process.argv.includes('--watch');

/**
 * Plugin: externalize @wordpress/* imports → window.wp.*
 */
const wpExternalsPlugin: esbuild.Plugin = {
  name: 'wp-externals',
  setup(build) {
    build.onResolve({ filter: /^@wordpress\// }, (args) => ({
      path: args.path,
      namespace: 'wp-external',
    }));

    build.onLoad({ filter: /.*/, namespace: 'wp-external' }, (args) => {
      // @wordpress/blocks → window.wp.blocks
      const wpModule = args.path.replace('@wordpress/', '');
      // Convert kebab-case to camelCase: block-editor → blockEditor
      const camel = wpModule.replace(/-([a-z])/g, (_: string, c: string) => c.toUpperCase());
      return {
        contents: `module.exports = window.wp.${camel};`,
        loader: 'js',
      };
    });
  },
};

// Discover block entry points
const blocksDir = path.resolve('src/ts/blocks');
const blockEntries: Record<string, string> = {};
if (fs.existsSync(blocksDir)) {
  for (const file of fs.readdirSync(blocksDir)) {
    if (file.endsWith('.ts') && !file.endsWith('.d.ts')) {
      const name = file.replace('.ts', '');
      blockEntries[`blocks/${name}`] = path.join(blocksDir, file);
    }
  }
}

// Common options
const commonOptions: esbuild.BuildOptions = {
  bundle: true,
  format: 'iife',
  target: 'es2020',
  minify: isProd,
  sourcemap: !isProd,
  logLevel: 'info',
};

async function build() {
  // Theme main bundle
  const themeOptions: esbuild.BuildOptions = {
    ...commonOptions,
    entryPoints: ['src/ts/theme.ts'],
    outfile: 'dist/theme.js',
  };

  // Block bundles
  const blockOptions: esbuild.BuildOptions = {
    ...commonOptions,
    entryPoints: blockEntries,
    outdir: 'dist',
    plugins: [wpExternalsPlugin],
  };

  if (isWatch) {
    const themeCtx = await esbuild.context(themeOptions);
    await themeCtx.watch();
    console.log('[esbuild] Watching theme.ts...');

    if (Object.keys(blockEntries).length > 0) {
      const blockCtx = await esbuild.context(blockOptions);
      await blockCtx.watch();
      console.log('[esbuild] Watching blocks...');
    }
  } else {
    await esbuild.build(themeOptions);
    if (Object.keys(blockEntries).length > 0) {
      await esbuild.build(blockOptions);
    }
  }
}

build().catch((err) => {
  console.error(err);
  process.exit(1);
});
