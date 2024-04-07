import { Image } from 'react-native';
import { Asset } from 'expo-asset';
import preloadImages from './preloadImages';

const cacheImages = (images: { [s: string]: unknown; } | ArrayLike<unknown>) => {
  return Object.values(images).map((image) => {
    if (typeof image === 'string') {
      return Image.prefetch(image);
    }
    return Asset.fromModule(image).downloadAsync();
  });
};

const loadAssetsAsync = async () => {
  const imageAssets = cacheImages(preloadImages);
  return Promise.all([...imageAssets]);
};

export default {
  cacheImages,
  loadAssetsAsync
};
