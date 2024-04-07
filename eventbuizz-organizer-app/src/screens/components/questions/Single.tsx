import React, { useState } from 'react';
import { useEffect } from 'react';
import { NestableScrollContainer, NestableDraggableFlatList } from "react-native-draggable-flatlist"
import { Pressable, HStack, Text, Flex, IconButton, Icon, Input, Spacer, Box, TextArea, Checkbox, Button, FormControl } from 'native-base';
import { Ionicons } from '@expo/vector-icons';




const Single = ({item_data, type}) => {
  const data_1 = [...Array(2)].map(() => ({
    key: `item-${Math.floor(Math.random() * 10000)}`,
    value: '',
    correct: false
  }));
  const [data, setData] = useState(data_1);
  useEffect(() => {
    item_data(data);
  }, [data])
  const renderItem = ({ item,drag, isActive }: any) => {
    return (
      <HStack my="2" space="3" alignItems="center" w="100%"  bg={isActive ? '#fff' : 'transparent'}>
        <IconButton
          variant="unstyled"
          rounded="50px"
          w="25px"
          h="25px"
          p="0"
          icon={<Icon size="md" as={Ionicons} name="menu"  />}
          onLongPress={drag}
        />
        <Input onChange={(e: any) => handleChange(item.key, e.target.value)} type="text" value={item.value} w="60%" rounded="6px" height={35} placeholder={type ? 'Column' : 'Option'}  />
        <Spacer />
        {!type && <Checkbox onChange={(e) => handleSelect(item.key, e)} isChecked={item.correct} size="sm" value="checkbox">
          <Text fontSize="xs">Correct</Text>
        </Checkbox>}
        {data.length > 1 && <IconButton
          variant="unstyled"
          rounded="50px"
          w="25px"
          h="25px"
          p="0"
          onPress={() => handleDelete(item.key)}
          icon={<Icon size="md" as={Ionicons} name="close"  />}
        />}
      </HStack>
    )
  };
  const handleChange = (id: any, value: string) => {
    const _id = data.findIndex((item: { key: any; }) => item.key === id)
    const _data =data[_id];
    _data.value = value; 
    data.splice(_id,1,_data)
    setData([...data])
  }
  const handleSelect = (id: any, value: string) => {
    const _id = data.findIndex((item: { key: any; }) => item.key === id)
    const _data =data[_id];
    _data.correct = value; 
    data.splice(_id,1,_data)
    setData([...data])
  }
  const handleDelete = (id: any) => {
    const _id = data.findIndex((item: { key: any; }) => item.key === id)
    data.splice(_id,1)
    setData([...data])
  }
  const handleAddMore = () => {
    const _item = {
      key: `item-${Math.floor(Math.random() * 10000)}`,
      value: '',
      correct: false
    };
    const _data = [...data,_item];
    setData(_data)
  }
  return (
    <Flex mb="3" w="100%" direction="column">
      <NestableScrollContainer>
        <NestableDraggableFlatList
          data={data}
          renderItem={renderItem}
          keyExtractor={(item) => item.key}
          onDragEnd={({ data }) => setData(data)}
        />
        <Button
          maxW="200px"
          onPress={handleAddMore}
        >
          {type ? 'Click to add column' : 'Click to add option'}
        </Button>
      </NestableScrollContainer>
    </Flex>
  )
}

export default Single