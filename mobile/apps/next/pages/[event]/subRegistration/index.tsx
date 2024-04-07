import IndexTemplate from 'application/screens/web/sugRegistration/Index';
import AfterLoginLayout from 'application/screens/web/layouts/AfterLoginLayout'

const Index = (props:any) => {
    return (
        <>
            <IndexTemplate {...props} />
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
        <AfterLoginLayout>
            {page}
        </AfterLoginLayout>
    )
}
export default Index

