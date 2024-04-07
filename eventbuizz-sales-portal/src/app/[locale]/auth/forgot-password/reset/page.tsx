"use client"; // this is a client component
import { useEffect, useState } from "react";
import Image from 'next/image';
import Illustration from '@/assets/img/illustration.png'
import { useRouter } from 'next/navigation';
import Loader from '@/components/forms/Loader';
import AlertMessage from "@/components/forms/alerts/AlertMessage";
import { useAppDispatch, useAppSelector } from "@/redux/hooks/hooks";
import { forgotPasswordReset, setLoading, setRedirect } from "@/redux/store/slices/AuthSlice";
import { RootState } from "@/redux/store/store";
import { useTranslations } from "next-intl";
import ErrorMessage from "@/components/forms/alerts/ErrorMessage";


const languages = [{ id: 1, name: "English" }, { id: 2, name: "Danish" }];




export default function requestReset({params:{locale}}:{params:{locale:string}}) {
    const t = useTranslations('auth_forgot_password_reset');
    const et = useTranslations('messages');
    
    const [password, setPassword] = useState('');
    const [passwordType, setPasswordType] = useState(true)
    const [passwordConfirmation, setPasswordConfirmation] = useState('');
    const [confirmpasswordType, setConfirmPasswordType] = useState(true)
    
    const dispatch = useAppDispatch();
    const router = useRouter();
    const {loading, redirect,  forgetPasswordEmail, forgetPasswordToken } = useAppSelector((state: RootState) => state.authUser);
    const [render, setRender] = useState(false)
    const [error, setError] = useState(false);
    
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
        setError(false);
        
        if(password === '' || passwordConfirmation === '' || (password !== passwordConfirmation)){
            setError(true);
        }

        if(password !== '' && passwordConfirmation !== '' && (password === passwordConfirmation)){
            dispatch(forgotPasswordReset({reset_code: forgetPasswordToken, email: forgetPasswordEmail, password: password, password_confirmation: passwordConfirmation}))
        }
    }


    return (
        <>
            {error && <ErrorMessage 
                icon= {"info"}
                title= {et('errors.invalid_data')}
                error= {t('confirm_password_mismatch_label')}
            />}
            <h2>{t('page_title')}</h2>
            <p>{t('page_subtitle')}</p>
            <form role="" onSubmit={handleSubmit}>
                <div className="form-area-signup">
                    <div className='form-row-box'>
                        <span className="icon-eye">
                            <Image onClick={() => setPasswordType(!passwordType)}  src={require(`@/assets/img/${passwordType ? 'close-eye':'icon-eye'}.svg`)} width="17" height="17" alt="" />
                        </span>
                        <input autoComplete="false" className={password ? 'ieHack' : ''} value={password} type={passwordType ? 'password' : 'text'} name="password" id="password" onChange={(e) => setPassword(e.target.value)} required  />
                        <label className="title">{t('new_password_label')}</label>
                    </div>
                    <div className='form-row-box'>
                        <span className="icon-eye">
                            <Image  onClick={() => setConfirmPasswordType(!confirmpasswordType)} src={require(`@/assets/img/${confirmpasswordType ? 'close-eye':'icon-eye'}.svg`)} width="17" height="17" alt="" />
                        </span>
                        <input autoComplete="false" className={passwordConfirmation ? 'ieHack' : ''} value={passwordConfirmation} type={confirmpasswordType ? 'password' : 'text'} name="password_confirmation" id="password_confirmation" required onChange={(e) => setPasswordConfirmation(e.target.value)}  />
                        <label className="title">{t('confirm_password_label')}</label>
                    </div>
                    <div className="form-row-box button-panel">
                        <button className="btn btn-primary" disabled={loading} type='submit'>{loading ? t('reset_button_reseting_label') : t('reset_button_label')}</button>
                    </div>
                </div>
            </form>
        </>
    );
}
