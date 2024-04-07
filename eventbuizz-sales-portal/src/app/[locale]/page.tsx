"use client";
import Image from 'next/image'
import { Inter } from 'next/font/google'
import styles from '@/assets/css/page.module.css'

import { useAppDispatch, useAppSelector } from "@/redux/hooks/hooks";

import Login from '@/app/[locale]/auth/login/page';
import Events from '@/app/[locale]/manage/events/page';
import { useEffect } from 'react';
import { useRouter } from 'next/navigation';
import Loader from '@/components/forms/Loader';

const inter = Inter({ subsets: ['latin'] })

export default function Home({params: {locale}}:any) {
  const user = useAppSelector((state)=>state.authUser.user);
  const router = useRouter();
  useEffect(() => {
    if(user !== null){
      console.log(`/${locale}/auth/login`);
      router.push(`/${locale}/manage/events`);
    }else{
      router.push(`/${locale}/auth/login`);
    }
  }, []);
  
  return (
    <>
      <Loader className='' fixed=''/>
    </>
  )
}

