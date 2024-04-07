import React from 'react'
import { Box, HStack, Spacer, Text, VStack } from 'native-base'

const QuestionType = ({data}) => {
  return (
    <>
      <Box  bg="#EFFFEF"  px="4" py="3" rounded="0" w="100%">
        <VStack w="100%" space="1" alignItems="flex-start">
          <HStack w="100%" space="0" alignItems="flex-start">
            <Text color="primary.default" fontSize="10px">This is the Option 1</Text>
            <Spacer />
            <Text fontSize="10px">70% | 512</Text>
          </HStack>
          <HStack w="100%" space="3" alignItems="flex-start">
            <Text color="primary.default" fontSize="10px">This is the Option 1</Text>
            <Spacer />
            <Text fontSize="10px">70% | 512</Text>
          </HStack>
          <HStack w="100%" space="3" alignItems="flex-start">
            <Text color="primary.default" fontSize="10px">This is the Option 1</Text>
            <Spacer />
            <Text fontSize="10px">70% | 512</Text>
          </HStack>
        </VStack>
      </Box>
    </>
  )
}

export default QuestionType