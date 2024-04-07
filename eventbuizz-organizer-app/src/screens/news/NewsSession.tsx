import React from 'react'
import { Dimensions } from 'react-native'
import { Ionicons } from '@expo/vector-icons'
import { Alert, Button, Center, Container, Divider, HStack, Icon, IconButton, ScrollView, Text, VStack } from 'native-base'
import FooterScreen from '@src/screens/components/FooterScreen';
import HeaderDashboard from '@src/screens/dashboard/HeaderDashboard';
import Pagination from '@src/screens/dashboard/Pagination';

const NewsSession = ({ navigation }: any) => {
  const [_width, set_width] = React.useState(324)
  const container = React.useRef()
  const heigth = Dimensions.get('window').height;
  const width = Dimensions.get('window').width;
  React.useEffect(() => {
    set_width(container.current.clientWidth - 180 - 155 - 115 -120 -110 - 70)
  }, [])
  
  return (
    <Container bg="#fff" w="100%" maxW="100%" h="100%" alignItems="flex-start" justifyContent="flex-start">
      <HeaderDashboard />
      <Center h={heigth - 198} px="8" py="8" justifyContent="flex-start" w="100%">
        <HStack mb="3" w="100%"  space="3" alignItems="center" justifyContent="flex-start">
          <Text  fontSize="lg" bold>News and Updates</Text>
          <IconButton
            variant="solid"
            p="0"
            size="sm"              
            icon={<Icon size="md" as={Ionicons} name="add-outline" color="white" />}
            onPress={()=>{
              console.log('hello')
            }}
          />
        </HStack>
        <Container ref={container} w="100%" maxW="100%" alignItems="flex-start" justifyContent="flex-start">
          <HStack px="4" py="3" w="100%" space="3" alignItems="center">
            <Center px="2" alignItems="flex-start" justifyContent="flex-start" w="180px">
              <Text fontSize="sm" bold>Title</Text>
            </Center>
            <Center px="2" justifyContent="flex-start" alignItems="flex-start" w={_width}>
              <Text fontSize="sm" bold>Description</Text>
            </Center>
            <Center px="2" justifyContent="flex-start" alignItems="flex-start" w="155px">
              <Text fontSize="sm" bold>Date</Text>
            </Center>
            <Center px="2" justifyContent="flex-start" alignItems="flex-start" w="135px">
              <Text fontSize="sm" bold>Send to</Text>
            </Center>
            <Center px="2" justifyContent="flex-start" alignItems="flex-start" w="90px">
              <Text fontSize="sm" bold>Send by</Text>
            </Center>
            <Center px="2" justifyContent="flex-start" alignItems="flex-start" w="110px" />
          </HStack>
          <Container overflow="hidden" borderWidth="1" borderColor="#e4e4e4" rounded="lg" w="100%" maxW="100%" alignItems="flex-start" justifyContent="flex-start">
            <ScrollView w="100%" h="400px">
              {[...Array(10)].map((item,k) =>
                <HStack bg={k%2 === 0 ? '#fff' : '#F7F7F7' } px="4" py="3" key={k} w="100%" space="3" alignItems="center">
                  <Center px="2" alignItems="flex-start" justifyContent="flex-start" w="180px">
                    <Text color="primary.default" fontSize="xs">News Events Title No 1</Text>
                  </Center>
                  <Center px="2" justifyContent="flex-start" alignItems="flex-start" w={_width}>
                    <Text color="primary.default" fontSize="xs">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration</Text>
                  </Center>
                  <Center px="2" justifyContent="flex-start" alignItems="flex-start" w="155px">
                    <Alert mb="1" px="3" rounded="sm" py="1" alignItems="flex-start" status="warning" bg="#FFF4D7">
                      <Text fontSize="10px" color="#F3842F" bold>Scheduled</Text>
                    </Alert>
                    
                    <Text color="primary.default" fontSize="xs">21 May 2023 - 12:45 am</Text>
                  </Center>
                  <Center px="2" justifyContent="flex-start" alignItems="flex-start" w="135px">
                    <Text color="primary.default" fontSize="xs">All Attendees</Text>
                  </Center>
                  <Center px="2" justifyContent="flex-start" alignItems="flex-start" w="90px">
                    <Text color="primary.default" fontSize="xs">Email</Text>
                  </Center>
                  <Center px="2" justifyContent="flex-end" alignItems="flex-start" w="110px">
                    <HStack w="100%" justifyContent="center"  space="3" alignItems="center">
                      <IconButton
                        p="0"
                        variant="unstyled"
                        icon={<Icon size="md" as={Ionicons} name="ios-create-outline" color="#bbb" />}
                        onPress={()=>{
                          console.log('hello')
                        }}
                      
                      />
                      <IconButton
                        p="0"
                        variant="unstyled"
                        icon={<Icon size="md" as={Ionicons} name="ios-trash-outline" color="#bbb" />}
                        onPress={()=>{
                          console.log('hello')
                        }}
                      
                      />
                    </HStack>
                    
                  </Center>
                </HStack>)}
            </ScrollView>
            
          </Container>
          
        </Container>
        
        <Pagination />
      </Center>
      <FooterScreen navigation={navigation} />
    </Container>
  )
}

export default NewsSession;