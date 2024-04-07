import React from 'react'
import { Text, HStack, Icon, IconButton, Spacer, Button } from 'native-base';
import { Ionicons } from '@expo/vector-icons'

const Pagination = ({ handle_page, current_page, total_pages}: any) => {
  return (
    <HStack w="100%" pt="4" space="3" alignItems="center">
      <Text fontSize="sm" color="primary.default">Showing {current_page} Events out of {total_pages} </Text>
      <Spacer />
      <HStack space="3" alignItems="center">
        <IconButton
          variant="solid"
          bg="#F7F7F7"
          p="2"
          icon={<Icon size="md" as={Ionicons} name="chevron-back-outline" color="#231F20" />}
          onPress={()=>{
            handle_page('prev')
          }}
          
        />
        {[...Array(total_pages)].map((item,k) => 
          <Button
            key={k}
            variant={ (k+1) === current_page ? 'solid' : 'outline'}
            color='primary.default'
            h="35px"
            borderColor={'#E0E0E0'}
            p="1"
            minW="35px"
            onPress={()=>{
              handle_page(k+1)
            }}
        
          >
            {k+1}
          </Button>)}
        <IconButton
          variant="solid"
          bg="#F7F7F7"
          p="2"
          icon={<Icon size="md" as={Ionicons} name="chevron-forward-outline" color="#231F20" />}
          onPress={()=>{
            handle_page('next')
          }}
          
        />
        
      </HStack>
      
    </HStack>
    
    
    
  )
}

export default Pagination;