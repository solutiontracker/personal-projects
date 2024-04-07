"use client"; // this is a client component
import {useEffect, useState} from "react";
import { useRouter } from 'next/navigation';
import Loader from '@/components/forms/Loader';
import { useAppDispatch, useAppSelector } from "@/redux/hooks/hooks";
import { RootState } from "@/redux/store/store";
import { forgotPasswordVerify, setForgetPasswordToken, setLoading, setRedirect } from "@/redux/store/slices/AuthSlice";
import ErrorMessage from "@/components/forms/alerts/ErrorMessage";
import { useTranslations } from "next-intl";

const languages = [{ id: 1, name: "English" }, { id: 2, name: "Danish" }];

export default function verifyResetCode({params:{locale}}:{params:{locale:string}}) {
    const t = useTranslations('auth_forgot_password_verify');
    const et = useTranslations('messages');

    const dispatch = useAppDispatch();
    const router = useRouter();
    const {loading, redirect, error, errors, forgetPasswordEmail } = useAppSelector((state: RootState) => state.authUser);
    const [token, setToken] = useState('');
    const [render, setRender] = useState(false)
    
    useEffect(() => {
        if(forgetPasswordEmail !== null){
            setRender(true);
        }else{
            router.push(`/${locale}/auth/forgot-password/request`);
        }
    }, [])

    useEffect(() => {
        if(redirect !== null) {
            dispatch(setRedirect(null));
            dispatch(setLoading(null));
            router.push(`/${locale}/${redirect}`);
        }
    }, [redirect]);

    const handleSubmit = (e:any) => {
        e.preventDefault();
        e.stopPropagation();
        dispatch(forgotPasswordVerify({token, email:forgetPasswordEmail}));
    }

    if(!render){
        return <Loader className='' fixed=''/>;
    }

    return (
        <>
            {errors && errors.length > 0 && <ErrorMessage 
                icon= {"info"}
                title= {et('errors.invalid_data')}
                errors= {errors}
            />}
            {error && <ErrorMessage 
                icon= {"info"}
                title= {et('errors.someting_went_wrong')}
                error= {error}
            />}
            <h2>{t('page_title')}</h2>
            <p>{t('page_subtitle')}</p>
            <form role="" onSubmit={handleSubmit}>
                <div className="form-area-signup">
                    <div className='form-row-box'>
                        <input  pattern="^[0-9]+$" maxLength={6} className={token ? 'ieHack' : '' } value={token} type="text" name="token" id="token" onChange={(e) => setToken(e.target.value)} required />
                        <label className="title">{t('reset_code_label')}</label>
                    </div>
                    <div className="form-row-box button-panel">
                        <button className="btn btn-primary" disabled={loading} type='submit'>{loading ? t('verify_button_verifying_label') : t('verify_button_label')}</button>
                    </div>
                </div>
            </form>
        </>
    );
}
