import ChooseProviderScreen from 'application/screens/web/auth/ChooseProvider'
import AuthLayout from 'application/screens/web/layouts/AuthLayout'
import BackgroundLayout from 'application/screens/web/layouts/BackgroundLayout'

const ChooseProvider = () => {
    return (
        <>
            <ChooseProviderScreen />
        </>
    )
}

export async function getServerSideProps() {
    return {
        props: {},
    }
}

ChooseProvider.getLayout = function getLayout(page:any) {
    return (
        <AuthLayout>
            <BackgroundLayout>{page}</BackgroundLayout>
        </AuthLayout>
      
    )
}
export default ChooseProvider