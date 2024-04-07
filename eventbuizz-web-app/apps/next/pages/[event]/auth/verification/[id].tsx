import VerificationScreen from 'application/screens/web/auth/Verification'
import AuthLayout from 'application/screens/web/layouts/AuthLayout'
import BackgroundLayout from 'application/screens/web/layouts/BackgroundLayout'

const Verification = () => {
    return (
        <VerificationScreen />
    )
}

export async function getServerSideProps() {
    return {
        props: {},
    }
}

Verification.getLayout = function getLayout(page:any) {
    return (
        <AuthLayout>
            <BackgroundLayout>{page}</BackgroundLayout>
        </AuthLayout>
      
    )
}

export default Verification