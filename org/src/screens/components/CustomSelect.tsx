/* eslint-disable @typescript-eslint/no-unsafe-return */
import { Box, Icon, Menu, Pressable, Text } from 'native-base'
import { Ionicons } from '@expo/vector-icons'
import React from 'react'

function printName (items:any, field:any) {
  const list = items.find((item):any => item.id === field);
  if (list) {
    return list.name
  } else {
    return 'Please Select'
  }
  //return list.name
}

const CustomSelect = ({width,title,required,value, items, onSelect, name}:any) => {
  const [menuTitle, setmenuTitle] = React.useState(value ? value : 'Please select');

  return (
    <Menu
      w="100%"
      p="0"
      pt="0"
      shouldOverlapWithTrigger={false} 
      trigger={(triggerProps) => {
        return (
          <Pressable w="100%" {...triggerProps}>
            <Box mb="1" px="4" py="2" rounded="lg" borderWidth="1" borderColor="#E0E0E0" w="100%">
              <Text color="#BBB" fontSize="xs" bold>{title} {required && '*'}</Text>
              <Text fontSize="xs">{printName(items, value)}</Text>
            </Box>
          </Pressable>
        )
      }}
    >

      <Box w={width} overflow="hidden" rounded="sm" bg="#ffffff">
        {items && items.map((item: string,k: number) => 
          <React.Fragment key={`key-menu-${k}`}> 
            <Menu.Item _focus={{backgroundColor: value === item.id ? '#F7F7F7' : 'transparent'}} bg={value === item.id ? '#F7F7F7' : 'transparent'}
              onPress={() => onSelect(name,item.id)}
              w="630px" _text={{fontSize: '12px', color: 'primary.default'}}>{item.name}</Menu.Item>
          </React.Fragment>
        )}
      </Box>

    </Menu>
  )
}

export default CustomSelect