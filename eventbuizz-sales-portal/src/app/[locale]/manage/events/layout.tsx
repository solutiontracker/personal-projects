"use client";
import '@/assets/css/app.scss'
import Image from 'next/image';
import Illustration from '@/assets/img/illustration.png';
import { useAppDispatch, useAppSelector } from '@/redux/hooks/hooks';
import { RootState } from '@/redux/store/store';
import { usePathname, useRouter } from 'next/navigation';
import { logOutUser } from '@/redux/store/slices/AuthSlice';
import { useEffect, useTransition } from 'react';
import { useLocale, useTranslations } from 'next-intl';

const languages = [{ id: 1, name: "English", locale:'en' }, { id: 2, name: "Danish", locale:'da' }];


export default function RootLayout({ children, params}: { children: React.ReactNode, params: { locale:string, event_id: string } }) {
    const t = useTranslations('manage-events-layout');
    
    const router = useRouter();
    const {user} = useAppSelector((state: RootState) => state.authUser);
    const dispatch = useAppDispatch();
    const pathname = usePathname();
    
    useEffect(() => {
         (user === null) ? router.push('auth/login') : null;
    }, [user]);

    const [isPending, startTransition] = useTransition();
    const locale = useLocale();

    function onLanguageChange(value:string) {
        console.log(`/${value}${pathname}`, 'selectchange');
        let replaceUrl = value === 'en' ? pathname.replace('/da', '/en') :  `/${value}${pathname}`;
        window.location.href = replaceUrl;
    }
    
  return (
    <>
    <header className="header">
        <div className="container">
            <div className="row bottom-header-elements">
                <div className="col-8">
                    {pathname !== `${params.locale === 'da' ? '/da' : '' }/manage/events` ? <p>
                        <a href="#!" onClick={(e)=>{e.preventDefault(); 
                            console.log('pathname', pathname);
                            if(pathname.includes('invoice') || pathname.includes('create') || pathname.includes('edit')){
                                router.push(`/${params.locale}/manage/events/${pathname.split('/')[3]}/orders`);
                            }
                            else if(pathname.includes('orders')){
                                router.push(`/${params.locale}/manage/events`);
                            }
                            else{
                                router.push(`/${params.locale}/manage/events`);
                            }
                        }}>
                            <i className="material-icons">arrow_back</i> {t('return_to_list_label')}
                        </a>
                    </p>: null}
                </div>
                <div className="col-4 d-flex justify-content-end">
                    <ul className="main-navigation">
                        {<li>{user?.first_name} {user?.last_name} <i className="material-icons">expand_more</i>
                            <ul>
                                <li><a href="" onClick={(e)=>{e.preventDefault(); dispatch(logOutUser({}));}}>Logout</a></li>
                            </ul>
                        </li>}
                        <li>{locale === 'da' ? 'Danish' : 'English'} <i className="material-icons">expand_more</i>
                            <ul>
                                {languages.map((value, key) => {
                                    return (
                                        <li key={key}>
                                            <a onClick={(e)=>{e.preventDefault(); onLanguageChange(value.locale)}}>{value.name}</a>
                                        </li>
                                    );
                                })}
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <main className="main-section" role="main">
        <div className="container">
            <div className="wrapper-box">
                <div className="container-box main-landing-page" style={{position:'relative'}}>
                    {children}
                </div>
            </div>
        </div>
    </main>
    </>
  )
}
