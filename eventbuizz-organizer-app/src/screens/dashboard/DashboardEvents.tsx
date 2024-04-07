import React from 'react';
import moment from 'moment';
import { Box, Container, HStack, Icon, IconButton, Image, Spacer, Text, VStack } from 'native-base';
import { Ionicons } from '@expo/vector-icons'
import IcoChart from '@src/assets/icons/IcoChart';
import IcoSpeaker from '@src/assets/icons/IcoSpeaker';
import IcoQuestions from '@src/assets/icons/IcoQuestions';
import IcoClipboard from '@src/assets/icons/IcoClipboard';
import IcoClipboardinfo from '@src/assets/icons/IcoClipboardinfo';

const Item = ({navigation,order,list}: any) => {
  return (
    <HStack py="4" px="4" w="100%" bg={order%2 === 0 ? '#fff' : '#F7F7F7'} space="3" alignItems="center">
      {list?.event.id && <Text minW="95px" color="primary.default" fontSize="sm">#{list?.event.id}</Text>}
      <Box minW="95px">
        <Image
          source={{
            uri:'https://wallpaperaccess.com/full/317508.jpg'
          }}
          alt="Alternate Text"
          w="80px"
          h="38px"
          rounded="lg"
      
        />
      </Box>
      <VStack minW="110px" space="0">
        {list?.event.start_date && <Text color="primary.default" fontSize="sm">{moment(list?.event.start_date).format('DD MMM YYYY')}</Text>}
        {list?.event.end_date && <Text color="primary.default" fontSize="sm">{moment(list?.event.end_date).format('DD MMM YYYY')}</Text>}
      </VStack>
      {list?.event.name && <Text color="primary.default" fontSize="sm" isTruncated>{list?.event.name}</Text>}
      <Spacer />
      <HStack  space="1" alignItems="center">
        <IconButton
          variant="unstyled"
          icon={<IcoChart color="#bbb" />}
          onPress={()=>{
            console.log('hello')
          }}
        />
        <IconButton
          variant="unstyled"
          icon={<IcoSpeaker color="#bbb" />}
          onPress={()=>{
            console.log('hello')
          }}
        />
        <IconButton
          variant="unstyled"
          icon={<IcoQuestions color="#bbb" />}
          onPress={()=>{
            console.log('hello')
          }}
        />
        <IconButton
          variant="unstyled"
          icon={<IcoClipboard color="#bbb" />}
          onPress={()=>{
            console.log('hello')
          }}
        />
        <IconButton
          variant="unstyled"
          icon={<IcoClipboardinfo color="#bbb"  />}
          onPress={()=>{
            console.log('hello')
          }}
        />
        <IconButton
          variant="solid"
          bg="#F7F7F7"
          p="2"
          icon={<Icon size="md" as={Ionicons} name="chevron-forward-outline" color="#231F20" />}
          onPress={()=>{
            navigation.navigate('polls')
          }}
          
        />
        
        
      </HStack>
    
  
    </HStack>
  )
}

const DashboardEvents = ({ navigation, data }: any) => {
  return (
    <>
      {data && <Container overflow="hidden" rounded="lg" borderWidth="1" borderColor="#EBEBEB" w="100%" maxW="100%">
        {data.map( (item,k) => 
          <React.Fragment key={`item-${k}`}>
            <Item list={item} navigation={navigation} order={k}  />
          </React.Fragment>
        )}
      </Container>}
    </>
  )
}

export default DashboardEvents