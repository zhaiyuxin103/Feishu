/**
 * @filename: lint-staged.config.mts
 * @type {import('lint-staged').Configuration}
 */
export default {
  '*.php': ['./vendor/bin/pint'],
};
