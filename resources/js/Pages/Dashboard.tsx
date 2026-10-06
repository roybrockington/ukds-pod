import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

type Booking = {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    subject: string | null;
    instagram: string | null;
    confirmed: boolean;
};

type Day = {
    label: string;
    slots: { time: string; bookings: Booking[] }[];
};

function StatusBadge({ confirmed }: { confirmed: boolean }) {
    return confirmed ? (
        <span className="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">
            Confirmed
        </span>
    ) : (
        <span className="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">
            Pending
        </span>
    );
}

function BookingDetails({ booking }: { booking: Booking }) {
    return (
        <div>
            <div className="flex flex-wrap items-center gap-2">
                <span className="font-medium text-gray-900">
                    {booking.name}
                </span>
                <StatusBadge confirmed={booking.confirmed} />

                <div className="ml-auto flex gap-2">
                    {!booking.confirmed && (
                        <Link
                            as="button"
                            method="patch"
                            href={route('bookings.confirm', booking.id)}
                            preserveScroll
                            className="inline-flex items-center rounded-md bg-gray-800 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25"
                        >
                            Confirm
                        </Link>
                    )}

                    <Link
                        as="button"
                        method="delete"
                        href={route('bookings.destroy', booking.id)}
                        preserveScroll
                        onBefore={() =>
                            confirm(
                                booking.confirmed
                                    ? `Delete ${booking.name}'s confirmed booking? This frees up the slot and can't be undone.`
                                    : `Decline ${booking.name}'s request? This frees up the slot and can't be undone.`,
                            )
                        }
                        className="inline-flex items-center rounded-md border border-red-300 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-widest text-red-700 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-25"
                    >
                        {booking.confirmed ? 'Delete' : 'Decline'}
                    </Link>
                </div>
            </div>

            {booking.subject && (
                <p className="mt-1 text-sm text-gray-700">{booking.subject}</p>
            )}

            <div className="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-500">
                <a
                    href={`mailto:${booking.email}`}
                    className="hover:text-gray-900 hover:underline"
                >
                    {booking.email}
                </a>
                {booking.phone && (
                    <a
                        href={`tel:${booking.phone}`}
                        className="hover:text-gray-900 hover:underline"
                    >
                        {booking.phone}
                    </a>
                )}
                {booking.instagram && (
                    <a
                        href={`https://instagram.com/${booking.instagram}`}
                        target="_blank"
                        rel="noreferrer"
                        className="hover:text-gray-900 hover:underline"
                    >
                        @{booking.instagram}
                    </a>
                )}
            </div>
        </div>
    );
}

export default function Dashboard({ days }: { days: Day[] }) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Schedule
                </h2>
            }
        >
            <Head title="Schedule" />

            <div className="space-y-8 py-12">
                {days.map((day) => {
                    const bookings = day.slots.flatMap((slot) => slot.bookings);
                    const confirmed = bookings.filter(
                        (b) => b.confirmed,
                    ).length;

                    return (
                        <section
                            key={day.label}
                            className="mx-auto max-w-7xl sm:px-6 lg:px-8"
                        >
                            <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                                <div className="flex flex-wrap items-baseline justify-between gap-2 border-b border-gray-200 px-6 py-4">
                                    <h3 className="text-lg font-semibold text-gray-900">
                                        {day.label}
                                    </h3>
                                    <p className="text-sm text-gray-500">
                                        {confirmed} confirmed ·{' '}
                                        {bookings.length - confirmed} pending
                                    </p>
                                </div>

                                <ul className="divide-y divide-gray-100">
                                    {day.slots.map((slot) => (
                                        <li
                                            key={slot.time}
                                            className="flex gap-6 px-6 py-4"
                                        >
                                            <div className="w-16 shrink-0 pt-0.5 text-sm font-medium tabular-nums text-gray-900">
                                                {slot.time}
                                            </div>

                                            <div className="min-w-0 flex-1 space-y-4">
                                                {slot.bookings.length > 0 ? (
                                                    slot.bookings.map(
                                                        (booking) => (
                                                            <BookingDetails
                                                                key={booking.id}
                                                                booking={
                                                                    booking
                                                                }
                                                            />
                                                        ),
                                                    )
                                                ) : (
                                                    <p className="pt-0.5 text-sm text-gray-400">
                                                        Available
                                                    </p>
                                                )}
                                            </div>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        </section>
                    );
                })}
            </div>
        </AuthenticatedLayout>
    );
}
