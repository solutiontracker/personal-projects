import * as React from 'react';
import PropTypes from 'prop-types';
import { useForm, SubmitHandler, Controller } from 'react-hook-form';
import validateEmail from '@src/utils/validations/ValidateEmail'
import UseAuthService from '@src/store/services/UseAuthService';
import { images } from '@src/styles';
import { Text, Input, Button, Image, Center, Flex, VStack, Pressable, FormControl } from 'native-base';

type Inputs = {
    email: string,
    password: string,
};


const Index = ({ navigation }: any) => {
  const { register, handleSubmit, watch, control, formState: { errors } } = useForm<Inputs>();
  const { isLoggedIn, processing, login, error } = UseAuthService();

  const onSubmit: SubmitHandler<Inputs> = input => {
    login({ email: input.email, password: input.password, logged: true, app_type: 'organizer' })
  };
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
            <Center w="100%" alignItems="flex-start">
              <Text mb="2" fontSize="lg" bold>User</Text>
              <FormControl isRequired isInvalid={'email' in errors || error !== ''}>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur, value } }) => (
                    <Input  onChangeText={(val) => onChange(val)}  type="text"  w="100%" placeholder="demo123@eventbuizz.com"  />
                  )}
                  name="email"
                  rules={{
                    required: 'Field is required',
                    validate: (value) =>
                      validateEmail(value) || 'Please enter valid email!',
                  }}
                  defaultValue=""
                />
                <FormControl.ErrorMessage>
                  {errors.email?.type === 'required'
                    ? 'Email is required'
                    : (error ? error : errors.email?.message)}
                </FormControl.ErrorMessage>
              </FormControl>
            </Center>
            <Center w="100%" alignItems="flex-start">
              <Text mb="2" fontSize="lg" bold>Password</Text>
              <FormControl isRequired isInvalid={'password' in errors}>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur, value } }) => (
                    <Input onChangeText={(val) => onChange(val)} type="password"  w="100%" placeholder="Password"  />
                  )}
                  name="password"
                  rules={{
                    required: 'Field is required'
                  }}
                  defaultValue=""
                />
                <FormControl.ErrorMessage>
                  {errors.password?.type === 'required'
                    ? 'Password is required'
                    : errors.password?.message}
                </FormControl.ErrorMessage>
              </FormControl>
            </Center>
            <Center w="100%" alignItems="flex-start">
              <Button
                w="100%"
                h="56px"
                _text={{fontWeight: 'bold'}}
                size="lg"
                rounded={'lg'}
                colorScheme="primary"
                isLoading={processing}
                onPress={handleSubmit(onSubmit)}
            
              >
              Login
              </Button>
              <Pressable
                w="100%"
                mt="5"
                textAlign="right"
                borderWidth="0"
                onPress={()=>{
                  navigation.navigate('auth');
                }}
              >
                <Text fontSize="md" underline>Forgot Password?</Text>
              </Pressable>
            </Center>
          </VStack>
        </Center>
      </Center>
    </Flex>
  );
};

Index.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default Index;
