import * as React from 'react';
import PropTypes from 'prop-types';
import { images } from '@src/styles';
import { Text, Input, Button, Image, Center, Flex, VStack, Radio, Pressable, HStack } from 'native-base';

const AuthenticationCode = ({ navigation }: any) => {
  const filed1 = React.useRef();
  const filed2 = React.useRef();
  const filed3 = React.useRef();
  const filed4 = React.useRef();
  const filed5 = React.useRef();
  React.useEffect(() => {
    filed1.current.focus();
  }, [])
  const handleChange = (e,field) => {
    e.preventDefault();
    if (!isNaN(Number(e.target.value))){
      if (field) {
        field.current.focus()
      }
    }
  }
  return (
    <Flex direction="row" w="100%" h="100%" alignItems="center" justifyContent="center">
      <Center w="100%" maxW="640px">
        <Text mb="5" fontSize="3xl" bold textTransform="uppercase">Event live management</Text>
        <Center bg="#fff" w="100%" maxW="640px" rounded="3" p="40px" >
          <Image
            mb="4"
            source={images.Logo}
            alt="Logo"
            w="240"
            h="82"
            
          />
          <VStack w="100%"  space="8">
            <Center w="100%" alignItems="center">
              <Text mb="3" fontSize="2xl" bold textTransform="uppercase">Two factor Authentication</Text>
              <Text mb="1" textAlign="center" fontSize="16px">Enter authentication code below we sent to email</Text>
              <Text textAlign="center" fontSize="16px" bold>demo123@eventbuizz.com</Text>
              
            </Center>
            <Center w="100%" alignItems="center">
              <HStack mb="8" space="5" alignItems="center">
                <Input borderWidth="0" keyboardType='number-pad' onKeyPress={(e) => handleChange(e,filed2)} ref={filed1} textAlign="center" fontWeight="bold" h="60px" w="60px" />
                <Input borderWidth="0" keyboardType='number-pad' onKeyPress={(e) => handleChange(e,filed3)} ref={filed2} textAlign="center" fontWeight="bold" h="60px" w="60px" />
                <Input borderWidth="0" keyboardType='number-pad' onKeyPress={(e) => handleChange(e,filed4)} ref={filed3} textAlign="center" fontWeight="bold" h="60px" w="60px" />
                <Input borderWidth="0" keyboardType='number-pad' onKeyPress={(e) => handleChange(e)} ref={filed4} textAlign="center" fontWeight="bold" h="60px" w="60px" />
              </HStack>
              <Text textAlign="center" w="100%" fontSize="lg" bold color="primary.500">04:34</Text>
              <Pressable
                mt="1"
                w="100%"
                textAlign="center" 
                borderWidth="0"
                onPress={()=>{
                  navigation.navigate('auth');
                }}
              
              >
                <Text fontSize="md" underline>Resend Code</Text>
              </Pressable>
             
            </Center>
            <Center w="100%" alignItems="flex-start">
              <Button
                w="100%"
                _text={{fontWeight: 'bold'}}
                size="lg"
                h="56px"
                rounded="lg"
                colorScheme="primary"
                onPress={()=>{
                  navigation.navigate('dashboard');
                }}
            
              >
              Send
              </Button>
              <Pressable
                mt="5"
                w="100%"
                textAlign="center" 
                borderWidth="0"
                onPress={()=>{
                  navigation.navigate('auth');
                }}
              
              >
                <Text fontSize="md">Back</Text>
              </Pressable>
              
            </Center>
          
          </VStack>
          
        </Center>
      </Center>
      
        
    </Flex>
  );
};

AuthenticationCode.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default AuthenticationCode;
