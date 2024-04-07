import React, { Component } from 'react';
import { View, StyleSheet, ImageBackground, Platform } from 'react-native';
import { Router, Switch, Route } from './routing';
import Home from './components/Home';
const image = { uri: "https://picsum.photos/3500"};


class App extends Component {
  state = {
    selectedPokemon: null
  };
  selectPokemon = selectedPokemon => {
    this.setState({
      selectedPokemon
    });
  };
  render() {
    return (
      <View style={styles.container}>
        <ImageBackground style={styles.banner} source={image}>
          <Router>
            <Switch>
              <Route exact path="/" render={props => (<Home {...props} selectPokemon={this.selectPokemon} />)} />
            </Switch>
          </Router>
        </ImageBackground>
      </View>
    );
  }
}
const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    height: '100%'
  },
   banner: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    height: '100%',
    width: '100%',
    resizeMode: "cover",
    backgroundColor: '#1D9FE4',
  }
});
export default App;