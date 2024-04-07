import Login from 'application/screens/web/auth/Login'
import AuthLayout from 'application/screens/web/layouts/AuthLayout'
import BackgroundLayout from 'application/screens/web/layouts/BackgroundLayout'

const Index = () => {
    return (
        <>
            <Login />
        </>
    )
}

export async function getServerSideProps() {
    return {
        props: {},
    }
}

Index.getLayout = function getLayout(page:any) {
    return (
        <AuthLayout>
            <BackgroundLayout>{page}</BackgroundLayout>
        </AuthLayout>
      
    )
}

export default Index
