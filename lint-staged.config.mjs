/**
 * @filename: lint-staged.config.mts
 * @type {import('lint-staged').Configuration}
 */
export default {
  '*.php': ['./vendor/bin/pint'],
  '*.{js,mjs,jsx,ts,tsx,json,css,scss,md,yml,yaml,html,vue}': [
    'prettier . --write',
  ],
};
