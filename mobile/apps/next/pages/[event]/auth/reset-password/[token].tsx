import ResetPasswordScreen from 'application/screens/web/auth/ResetPassword'
import AuthLayout from 'application/screens/web/layouts/AuthLayout'
import BackgroundLayout from 'application/screens/web/layouts/BackgroundLayout'

const ResetPassword = () => {
    return (
        <>
            <ResetPasswordScreen />
        </>
    )
}

export async function getServerSideProps() {
    return {
        props: {},
    }
}

ResetPassword.getLayout = function getLayout(page:any) {
    return (
        <AuthLayout>
            <BackgroundLayout>{page}</BackgroundLayout>
        </AuthLayout>
      
    )
}

export default ResetPassword