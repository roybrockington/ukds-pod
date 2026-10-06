import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link } from '@inertiajs/react';
import { PropsWithChildren } from 'react';

export default function Guest({
    children,
    wide = false,
}: PropsWithChildren<{ wide?: boolean }>) {
    return (
        <div className="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0">
            <div>
                <Link href="/">
                    <ApplicationLogo className="h-32 w-auto" />
                </Link>
            </div>

            <div
                className={`mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:rounded-lg ${
                    wide ? 'sm:max-w-2xl' : 'sm:max-w-md'
                }`}
            >
                {children}
            </div>

            <footer className="py-6 text-sm text-gray-500">
                <Link
                    href={route('privacy')}
                    className="underline hover:text-gray-900"
                >
                    Privacy policy
                </Link>
            </footer>
        </div>
    );
}
