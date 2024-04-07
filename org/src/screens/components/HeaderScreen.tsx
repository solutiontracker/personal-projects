import React from 'react'
import { HStack, Text, VStack, Icon, Spacer, IconButton, Avatar, Center, Switch, Popover, Pressable } from 'native-base';
import { Ionicons, AntDesign } from '@expo/vector-icons'
import UseAuthService from '@src/store/services/UseAuthService';
import moment from 'moment';
const HeaderScreen = ({data} : any) => {
  const { logout } = UseAuthService()
  return (
    <HStack mb="7" pt="2" w="100%" space="3" alignItems="center">
      <VStack space="1">
        <Text fontSize="lg" bold>{data.name}</Text>
        <HStack  space="3" alignItems="center">
          <Text fontSize="xs"><Icon  as={Ionicons} size="sm" name="ios-calendar-outline" /> {moment(data?.start_date).format('DD MMM YYYY')}</Text>
          <Text rounded="sm" px="2" py="1" bg="#F7F7F7" fontSize="xs">#{data.id}</Text>
        </HStack>
      </VStack>
      <Spacer />
      <HStack  space="3" alignItems="center">
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
          }}>
            EA
        </Avatar>
      </HStack>
    </HStack>
  )
}

export default HeaderScreen;