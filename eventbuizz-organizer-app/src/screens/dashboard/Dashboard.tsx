
import React from 'react'
import { useState } from 'react';
import { Button, Center, Container, HStack, Icon, Spinner, Text } from 'native-base';
import HeaderDashboard from './HeaderDashboard';
import { Ionicons } from '@expo/vector-icons'
import DashboardEvents from './DashboardEvents';
import IcoActiveEvents from '@src/assets/icons/IcoActiveEvents';
import UseEventService from '@src/store/services/UseEventService';
import IcoFutureEvents from '@src/assets/icons/IcoFutureEvents';
import Pagination from './Pagination';
import { useEffect } from 'react';


const Dashboard = ({ navigation }: any) => {
  const { modules, loadModules, processing } = UseEventService();
  const [active, setactive] =  useState(true);
  const [pageLimit, setpageLimit] = useState(10);
  const [searcQuery, setsearcQuery] = useState('');
  const [sortyBy, setsortyBy] = useState('name');
  const [orderBy, setorderBy] = useState('desc');
  const [currentPage, setcurrentPage] = useState(1);
  


  const loadData = () => {
    loadModules({
      limit: pageLimit,
      page: currentPage,
      query: searcQuery,
      action: active ? 'active' : 'future',
      sort_by: sortyBy,
      order_by: orderBy
    });
  }

  useEffect(() => {
    loadData();
  }, []);

  
  useEffect(() => {
    setcurrentPage(1)
    loadData();
  }, [active]);
  
  useEffect(() => {
    const delayDebounceFn = setTimeout(() => {
      setcurrentPage(1)
      loadData();
    }, 500)

    return () => clearTimeout(delayDebounceFn)
  }, [searcQuery])

  const handleChange = (e: any) => {
    setsearcQuery(e.target.value);
  }
  const handlePages = (type: string): any => {
    if (type === 'prev' && currentPage > 1) {
      setcurrentPage(currentPage - 1)
      loadData();
    } else if (type === 'next' && currentPage < modules.lastPage) {
      const page_number = currentPage;
      setcurrentPage(page_number + 1)
      loadData();
    } else {
      setcurrentPage(type)
      loadData();
    }
  }
  return (
    <Container bg="#fff" w="100%" maxW="100%" h="100%" alignItems="flex-start" justifyContent="flex-start">
      <HeaderDashboard searchvalue={searcQuery} handleChange={handleChange} />
      {processing && 
      <HStack px="8" py="8" mx="auto" space={2} justifyContent="center" alignItems="center">
        <Spinner size="lg" accessibilityLabel="Loading posts" />
        <Text color="primary.500" fontSize="xl">
        Loading
        </Text>
      </HStack>}
      {!processing && modules.total === 0 && <Center px="8" py="8" justifyContent="center" w="100%"><Text  fontSize="xl">No Result Found</Text></Center>}
      {!processing && modules.total > 0 &&  <Center px="8" py="8" justifyContent="center" w="100%">
        <HStack mb="8" w="100%" borderBottomWidth="1" borderBottomColor="#EBEBEB"  space="16">
          <Button
            p="0"
            pb="4"
            variant="unstyled"
            _text={{fontSize: 'md',fontWeight: active? '600' : '400',paddingLeft: '2',color: active ? 'primary.heading' : 'primary.border'}}
            leftIcon={<IcoActiveEvents color={active ? '#231F20' : '#bbb' }  />}
            onPress={()=>{
              if (!active) {
                setcurrentPage(1)
                setactive(true)
              }
            }}
        
          >
          Active Events
          </Button>
          <Button
            p="0"
            pb="4"
            variant="unstyled"
            _text={{fontSize: 'md',color: !active ? 'primary.heading' : 'primary.border',paddingLeft: '2',fontWeight: !active? '600' : '400'}}
            leftIcon={<IcoFutureEvents color={!active ? '#231F20' : '#bbb' }  /> }
            onPress={()=> {
              if (active) {
                setcurrentPage(1)
                setactive(false)
              }
            }}
        
          >
            Future Events
          </Button>
         
        </HStack>
        <Center w="100%" maxW="100%" alignItems="flex-start">
          <HStack   py="3" px="4" pt="0" space="3" alignItems="center">
            <Text minW="95px" fontSize="sm" bold>Event ID <Icon as={Ionicons} name="chevron-down-outline"  /></Text>
            <Text minW="95px" fontSize="sm" bold>Event logo</Text>
            <Text minW="110px" fontSize="sm" bold>Date <Icon as={Ionicons} name="chevron-down-outline"  /></Text>
            <Text fontSize="sm" bold>Event Name <Icon as={Ionicons} name="chevron-down-outline"  /></Text>
          </HStack>
        
          <DashboardEvents data={modules?.result} navigation={navigation} /> 
          {modules?.hasMorePages && <Pagination handle_page={handlePages} current_page={currentPage} total_pages={modules.lastPage} />}
        </Center>
      </Center>}
      
    </Container>
    
    
  )
}

export default Dashboard;