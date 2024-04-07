import '/public/fonts/fonts.css'
import '@/assets/css/app.scss'
import {ReactNode} from 'react';
import {NextIntlClientProvider} from 'next-intl';
import {notFound} from 'next/navigation';

type Props = {
  children: ReactNode;
};

// Since we have a `not-found.tsx` page on the root, a layout file
// is required, even if it's just passing children through.
export default async function RootLayout({ children, params: {locale}}: { children: React.ReactNode, params: {locale:string} }) {
  let lang;
  try {
    lang = (await import(`../../lang/${locale}.json`)).default;
  } catch (error) {
    notFound();
  }
  return (
    <NextIntlClientProvider locale={locale} messages={lang}>
      {children}
    </NextIntlClientProvider>

  )
}