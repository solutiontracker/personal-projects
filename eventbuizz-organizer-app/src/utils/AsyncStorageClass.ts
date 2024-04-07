
import AsyncStorage from '@react-native-async-storage/async-storage';

class AsyncStorageClass {

  static setItem = async (key: string, value: any): Promise<any> => {
    try {
      return await AsyncStorage.setItem(key, JSON.stringify(value));
    } catch (error) {
      return null;
    }
  };

  static getItem = async (key: string): Promise<any> => {
    try {
      const item: any = await AsyncStorage.getItem(key);
      return JSON.parse(item);
    } catch (error) {
      return null;
    }
  }

  static mergeItem = async (key: string, value: any): Promise<any> => {
    try {
      return await AsyncStorage.mergeItem(key, JSON.parse(value));
    } catch (error) {
      return value;
    }
  }

  static removeItem = async (key: string) => {
    try {
      await AsyncStorage.removeItem(key);
    } catch (error) {
      console.log(error);
    }
  }

  static clear = async () => {
    try {
      await AsyncStorage.clear();
    } catch (error) {
      console.log(error);
    }
  }

}

export default AsyncStorageClass;