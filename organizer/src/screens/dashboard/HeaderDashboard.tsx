import React from 'react'
import { Text, HStack, Center, Image, Input, Icon, IconButton, Avatar, Popover, Button, VStack, Spacer, Pressable } from 'native-base';
import { images } from '@src/styles';
import { Ionicons, AntDesign } from '@expo/vector-icons'
import { Switch } from 'react-native-gesture-handler';
import UseAuthService from '@src/store/services/UseAuthService';

const HeaderDashboard = ({ navigation, handleChange, searchvalue }: any) => {
  const { logout } = UseAuthService();
  return (
    <HStack px="8"py="6" w="100%" bg="#F7F7F7" alignItems="center">
      <Center alignItems="flex-start" w="45%">
        <HStack space="6" alignItems="center">
          <Image
            source={images.Logosm}
            alt="Alternate Text"
            w="45px"
            h="70px"
            
          />
          
          <Center alignItems="flex-start">
            <Text lineHeight="sm" fontSize="2xl" bold>Events Organization</Text>
            <Text pt="1" lineHeight="sm" fontSize="lg">Owner Account</Text>
          </Center>
        </HStack>
        
      </Center>
      <Center w="55%" justifyContent="flex-end" alignItems="flex-end">
        <HStack  space="3" alignItems="center">
          <Input value={searchvalue} onChange={handleChange} InputLeftElement={<Icon as={Ionicons} ml="2" name="ios-search-outline" size="lg" />} placeholder="Search" text={{color: '#bbb',fontWeight: '700'}}  />
          <Popover
            crossOffset={-56}
            trigger={(triggerProps) => {
              return <IconButton
                variant="transparent"
                borderWidth="1"
                rounded="100px"
                borderColor="#E0E0E0"
              
                icon={<Icon size="lg" as={Ionicons} name="settings-outline"  />}
                {...triggerProps}
              
              />
            }}
            
          >
            <Popover.Content borderColor="rgba(0,0,0,0.08)" w="270px" shadow={0}>
              <Popover.Arrow bg="#fff" />
              <Popover.Body bg="#fff">
                <VStack py="3" space="5">
                  <HStack  space="3" alignItems="center">
                    <HStack space="2">
                      <Icon as={Ionicons} size="lg" name="ios-sunny"  />
                      <Center alignItems="flex-start">
                        <Text fontSize="14px">Theme</Text>
                        <Text color="primary.border" fontSize="xs">Light</Text>
                      </Center>
                     
                    </HStack>
                      
                  
                    <Spacer />
                    <Center>
                      <Switch />
                    </Center>
                  </HStack>
                  <HStack  space="3" alignItems="center">
                    <HStack space="2">
                      <Icon as={Ionicons} size="lg" name="ios-globe-outline"  />
                      <Center alignItems="flex-start">
                        <Text fontSize="14px">Language</Text>
                        <Text color="primary.border" fontSize="xs">English</Text>
                      </Center>
                     
                    </HStack>
                      
                  
                    <Spacer />
                    <Center>
                      <Switch />
                    </Center>
                  </HStack>
                  <HStack  space="3" alignItems="center">
                    <Pressable onPress={() => logout()}>
                      <HStack space="2">
                        <Icon as={AntDesign} size="md" name="logout"  />
                        <Center alignItems="flex-start">
                          <Text fontSize="14px">Logout</Text>
                        </Center>
                      </HStack>
                    </Pressable>
                  </HStack>
                 
                </VStack>
                
              </Popover.Body>
            </Popover.Content>
          </Popover>
          
          <Avatar
            bg="#FFF4D7"
            size="46px"
            _text={{color: '#F3842F', fontWeight: 400}}
            source={{
              uri:'https://pbs.twimg.com/profile_images/1369921787568422915/hoyvrUpc_400x400.jpg'
            }}
            
          >
            EA
          </Avatar>
          
          
          
        </HStack>
        
      </Center>
      
    </HStack>
    
    
  )
}

export default HeaderDashboard;