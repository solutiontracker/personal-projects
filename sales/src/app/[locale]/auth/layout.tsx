'use client';
import '@/assets/css/app.scss'
import Image from 'next/image';
import Loading from './loading';
import { useTransition } from 'react';
import { usePathname, useRouter } from 'next/navigation';
import { useLocale, useTranslations } from 'next-intl';

const languages = [{ id: 1, name: "English", locale:'en' }, { id: 2, name: "Danish", locale:'da' }];

export default function RootLayout({ children}: { children: React.ReactNode }) {
    const t = useTranslations('auth_layout');
    const [isPending, startTransition] = useTransition();
    const router = useRouter();
    const pathname = usePathname();
    const locale = useLocale();
    function onLanguageChange(value:string) {
        let newPathname = pathname.replace('/da', '').replace('/en', '');
        let replaceUrl = `/${value}${newPathname}`;
        window.location.href = replaceUrl;
    }

  return (
    <div className="signup-wrapper">
            <main className="main-section" role="main">
                <div className="container">
                    <div className="wrapper-box">
                        <div className="container-box">
                            <div className="row">
                                <div className="col-6">
                                    <div className="left-signup">
                                        <Image src={'/img/logo.svg'} alt="" width="150" height="32" className='logos' />
                                        <div className="text-block">
                                            <h4>{t('title')}</h4>
                                            <p>{t('subtitle')}</p>
                                            <ul>
                                                <li>{t('feature_one')}</li>
                                                <li>{t('feature_two')}</li>
                                                <li>{t('feature_three')}</li>
                                                <li>{t('feature_four')}</li>
                                            </ul>
                                        </div>
                                        <Image src={'/img/illustration.svg'} alt="" width="300" height="220" className='illustration' />
                                    </div>
                                </div>
                                <div className="col-6">
                                    <div className="right-section-blank">
                                        <ul className="main-navigation">
                                            <li>
                                                <a href="#!">
                                                    <i className="icons"><Image src={'/img/ico-globe.svg'} alt="" width="16" height="16" /></i>
                                                    <span id="language-switch">{locale === 'da' ? 'Danish' : 'English'}</span><i className="material-icons">keyboard_arrow_down</i>
                                                </a>
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
                                        <div className="right-formarea">
                                            {children}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

  )
}
