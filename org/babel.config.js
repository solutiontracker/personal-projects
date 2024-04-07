module.exports = function(api) {
  api.cache(true);
  return {
    presets: ['babel-preset-expo', '@babel/preset-typescript'],
    plugins: [
      [
        'module-resolver',
        {
          root: ['./src'],
          alias: {
            '@src': './src',
            '@screens': './src/screens',
            '@icons': './src/assets/icons',
          },
          extensions: [
            '.ios.js',
            '.android.js',
            '.js',
            '.jsx',
            '.json',
            '.tsx',
            '.ts',
            '.native.js',
            '.json'
          ],
        }
      ],
      "react-native-reanimated/plugin",
      [
        "module:react-native-dotenv",
        {
          envName: 'APP_ENV',
          moduleName: '@env',
          path: '.env'
        }
      ]
    ]
  };
};
