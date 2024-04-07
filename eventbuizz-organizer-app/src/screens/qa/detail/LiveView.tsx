import { useState } from 'react';
import React from 'react'
import { Box, Button, Center, Container, HStack, Icon, IconButton, Input, Pressable, ScrollView, Select, Spacer, Stack, Switch, Text } from 'native-base'
import { Ionicons } from '@expo/vector-icons';
import moment from 'moment';
import UseQaService from '@src/store/services/UseQaService';
import { useEffect } from 'react';
import { QaActions } from '@src/store/slices/Qa.Slice';
import { useDispatch } from 'react-redux'

const ItemBox = ({timer, data, status}: any) => {
  const [toggle, setToggle] = React.useState(timer || false);
  const { addlivetoacrhieve }  = UseQaService();
  const dispatch = useDispatch()
  return (
    <Container bg="#fff" maxW="100%" p="0" w="100%">
      {timer && 
        <HStack px="3" py="3" bg="green.100" w="100%" rounded="sm" alignItems="center">
          <Text fontSize="sm" bold>Active Question</Text>
          <Spacer />
          <HStack space="2" alignItems="center">
            <Text fontSize="16px" bold>00 : 04 : 31</Text>
            <IconButton
              p="0"
              variant="unstyled"
              icon={<Icon size="xl" as={Ionicons} name="ios-pause-circle-outline" color="danger.500" />}
              onPress={()=>{
                QaActions.openpopupAction({id: data.id, status: true})
              }}
            />
          </HStack>
        </HStack>}
      <Box w="100%" px="3" bg={status ? '#EDEDED' : '#fff'} py="2">
        <HStack mb="2" space="3" alignItems="center">
          <Center>
            <Text maxW="150px" isTruncated fontSize="xs" bold>{data.attendee.first_name}</Text>
          </Center>
          <Spacer />
          {!timer &&  <HStack space="1">
            {!status && <><IconButton
              variant="unstyled"
              p="0"
              icon={<Icon color="primary.500" size="md" as={Ionicons} name="ios-play-circle" />}
              onPress={()=>{
                console.log({id: data.id})
              }}/>
            <IconButton
              variant="unstyled"
              p="0"
              icon={<Icon color="#bbb" size="md" as={Ionicons} name="ios-download-outline" />}
              onPress={()=>{
                addlivetoacrhieve({id: data.id})
              }}/></>}
            <IconButton
              variant="unstyled"
              p="0"
              icon={<Icon color="#bbb" size="sm" as={Ionicons} name="ios-copy-outline" />}
              onPress={()=>{
                dispatch(QaActions.openpopupAction({id: data.id, status: true}))
              }}/>
          </HStack>}
        </HStack>
        <HStack mb="1" space="2" alignItems="center">
          <HStack space="1" alignItems="center">
            <Icon size="xs" as={Ionicons} name="ios-calendar-outline"  />
            <Text color="primary.default" fontSize="10px">{moment(new Date(data.created_at)).format('D MMMM, YYYY')}</Text>
          </HStack>
          <HStack space="1" alignItems="center">
            <Icon size="xs" as={Ionicons} name="ios-time-outline"  />
            <Text color="primary.default" fontSize="10px">{moment(new Date(data.created_at)).format('h:mm a')}</Text>
          </HStack>
        </HStack>
        {toggle && <React.Fragment>
          <Text mt="1" mb="2" color="primary.default" fontSize="xs">{data.info[0].value.replace(/<\/?[^>]+(>|$)/g, '')}</Text>
          <HStack mb="2" space="1" alignItems="center">
            <Text minW="75px" color="primary.default" fontSize="xs">Speakers :</Text>
            <Center alignItems="flex-start" w="calc(100% - 75px)">
              <Stack direction="row" flexWrap="wrap" space="2">
                <Button
                  size="sm"
                  px="3"
                  py="1"
                  bg="#F7F7F7"
                  _text={{color: 'primary.default'}}
                  variant="unstyled"
                  onPress={()=>{
                    console.log('hello')
                  }}
              
                >
                New speaker
                </Button>
                <Button
                  size="sm"
                  px="3"
                  py="1"
                  bg="#F7F7F7"
                  _text={{color: 'primary.default'}}
                  variant="unstyled"
                  onPress={()=>{
                    console.log('hello')
                  }}
              
                >
                sp 1
                </Button>
              
              </Stack>
            
            </Center>

          </HStack>
        </React.Fragment>}
      </Box>
      <Button
        w="100%"
        p="0"
        py="1"
        borderBottomRightRadius="3"
        borderBottomLeftRadius="3"
        rounded="0"
        bg="#F7F7F7"
        _text={{fontSize: 'xs', color: 'primary.default'}}
        colorScheme="primary"
        onPress={()=>{
          setToggle(!toggle)
        }}
      
      >
        <HStack w="100%" space="2" alignItems="center">
          <Text color="primary.default" fontSize="xs">{toggle ? 'Less' : 'More'}</Text>
          <Icon as={Ionicons} name={toggle ? 'chevron-up-outline' : 'chevron-down-outline'}  />
        </HStack>
       
      </Button>
    </Container>
    
  )
}

const LiveView = () => {
  const  { programs, innerProcessing } = UseQaService();
  const [data, setData] = useState(null);
  const [timer, settimer] = useState(false);
  const [toggle, setToggle] = useState(false)

  useEffect(() => {
    if (!toggle) {
      setData(programs.liveQA)
    } else {
      setData(programs.archiveQA)
    }
  }, [programs, innerProcessing])
  
  return (
    <>
      {data && <Container alignItems="flex-stat" justifyContent="flex-start" h="100%" w="100%" maxW="100%">
        <HStack mb="4" w="100%" space="3" alignItems="center">
          <HStack borderBottomWidth="1" borderColor="#EBEBEB" pb="2" space="5" alignItems="center">
            <Pressable
              p="0"
              varient="unstyled"
              borderWidth="0"
              onPress={()=>{
                setToggle(false)
                setData(programs.liveQA)
              }}
            >
              <Text color={!toggle ? '#000' : '#bbb'} fontSize="16px" bold>Live</Text>
              {!toggle && <Spacer h="2px" rounded="lg" w="100%" bg="#000" position="absolute" left="0" bottom="-10px" />}
            </Pressable>
            <Pressable
              p="0"
              varient="unstyled"
              borderWidth="0"
              onPress={()=>{
                setToggle(true)
                setData(programs.archiveQA)
              }}
            >
              <Text color={toggle ? '#000' : '#bbb'} fontSize="16px" bold>Archive</Text>
              {toggle && <Spacer h="2px" rounded="lg" w="100%" bg="#000" position="absolute" left="0" bottom="-10px" />}
            </Pressable>
          </HStack>
          
        </HStack>
        <Box bg="#F7F7F7" p="2" rounded="lg" w="100%" h="90%">
          <Input fontSize={'xs'} mb="2" size="sm" bg="#fff" h="40px" InputLeftElement={<Icon ml="2" as={Ionicons} size="sm" name="ios-search-outline" />} placeholder="Search"  />
          
          <Box overflow="hidden" w="100%" h="90%" bg="#fff" p="0" rounded="lg" borderWidth="1" borderColor="#EDEDED">
            <ScrollView w="100%" h="100%">
              {data.length !== 0 && data.map((item,k) => 
                <React.Fragment key={item.id}>
                  <ItemBox status={toggle} timer={timer} data={item} />
                </React.Fragment>
              )}
              {data.length === 0 && <Text p="4" fontSize="xs">No Data Found</Text>}
            </ScrollView>
          </Box>
        </Box>
        
      </Container>}
    </>
    
  )
}

export default LiveView