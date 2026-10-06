import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link } from '@inertiajs/react';

export default function BookingSuccess() {
    return (
        <GuestLayout>
            <Head title="Booking Requested" />

            <h1 className="text-lg font-semibold text-gray-900">
                Thanks, your booking request has been received
            </h1>
            <p className="mt-2 text-sm text-gray-600">
                Your slot isn't confirmed yet. We'll review your request and
                you'll receive an email once your booking has been confirmed.
            </p>

            <div className="mt-6">
                <Link
                    href={route('home')}
                    className="text-sm text-gray-600 underline hover:text-gray-900"
                >
                    Back to the booking form
                </Link>
            </div>
        </GuestLayout>
    );
}
