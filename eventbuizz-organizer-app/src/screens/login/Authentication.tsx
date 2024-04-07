import * as React from 'react';
import PropTypes from 'prop-types';
import { images } from '@src/styles';
import { Text, Input, Button, Image, Center, Flex, VStack, Radio, Pressable } from 'native-base';

const Authentication = ({ navigation }: any) => {

  
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
              <Text mb="3" fontSize="2xl" bold textTransform="uppercase">Two factor authentication</Text>
              <Text maxW="300px" textAlign="center" fontSize="16px">Please Select Preferred Method To Receive Authentication Code</Text>
              
            </Center>
            <Center w="100%" alignItems="center">
              <Radio.Group space="3" size="sm" name="MyRadioGroup">
                <Radio value="email" _text={{color: 'primary.default'}}>de****23@eventbuizz.com</Radio>
                <Radio value="phone" _text={{color: 'primary.default'}}>656*********56</Radio>
              </Radio.Group>
             
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
                  navigation.navigate('verify');
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
                  navigation.navigate('login');
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

Authentication.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default Authentication;
