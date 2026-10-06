import { ImgHTMLAttributes } from 'react';

export default function ApplicationLogo(
    props: ImgHTMLAttributes<HTMLImageElement>,
) {
    return (
        <img
            src="/logo.png"
            alt={import.meta.env.VITE_APP_NAME || 'Logo'}
            {...props}
        />
    );
}
