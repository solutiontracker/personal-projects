import ResetPasswordRequestScreen from 'application/screens/web/auth/ResetPasswordRequest'
import AuthLayout from 'application/screens/web/layouts/AuthLayout'
import BackgroundLayout from 'application/screens/web/layouts/BackgroundLayout'

const ResetPasswordRequest = () => {
    return (
        <>
            <ResetPasswordRequestScreen />
        </>
    )
}

export async function getServerSideProps() {
    return {
        props: {},
    }
}

ResetPasswordRequest.getLayout = function getLayout(page:any) {
    return (
        <AuthLayout>
            <BackgroundLayout>{page}</BackgroundLayout>
        </AuthLayout>
      
    )
}

export default ResetPasswordRequest