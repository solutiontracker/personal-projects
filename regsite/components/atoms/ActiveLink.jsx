import { useRouter } from 'next/router'
import Link from 'next/link'
import React, { Children } from 'react'

const ActiveLink = (props) => {
    const { asPath } = useRouter()
    const childClassName = props.className || ''
    const activeClassName = props.activeClassName || ''

    // pages/index.js will be matched via props.href
    // pages/about.js will be matched via props.href
    // pages/[slug].js will be matched via props.as
    const className =
        asPath.indexOf(props.href) !== -1 || asPath.indexOf(props.as) !== -1
            ? `${childClassName} ${activeClassName}`.trim()
            : childClassName

    return (
        <Link scroll={false} {...props}>
            {props.target === '_blank' ? 
                <a target="_blank" className={className}>{props.children}</a>:
                <a className={className}>{props.children}</a>
            }
        </Link>
    )
}

export default ActiveLink