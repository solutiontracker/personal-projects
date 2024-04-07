import React, { Component } from 'react';
import { View, Text, StyleSheet, Platform} from 'react-native';
import ViewPoint from './web/ViewPoint';

export default class Home extends Component {
  render() {
    return (
      <View style={styles.container}>
          {Platform.OS === 'web' ? <ViewPoint /> : <Text>This is for Mobile</Text>}
      </View>
    )
  }
}
const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    height: '100%',
    width: '100%',
  }
});