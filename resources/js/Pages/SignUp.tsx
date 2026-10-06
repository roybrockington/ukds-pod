import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler, useState } from 'react';

const DAYS = [
    { date: '2026-10-24', label: 'Saturday 24th October' },
    { date: '2026-10-25', label: 'Sunday 25th October' },
];

// Slots start at quarter past and quarter to the hour, from 9:15am to 5:45pm.
const TIMES = Array.from({ length: 9 }, (_, i) => 9 + i).flatMap((hour) =>
    [15, 45].map((minute) => ({
        value: `${String(hour).padStart(2, '0')}:${minute}`,
        label: `${hour > 12 ? hour - 12 : hour}:${minute}${hour >= 12 ? 'pm' : 'am'}`,
    })),
);

const optional = (label: string) => (
    <>
        {label} <span className="font-normal text-gray-500">(optional)</span>
    </>
);

export default function SignUp() {
    const [submitted, setSubmitted] = useState(false);

    const { data, setData, errors } = useForm({
        name: '',
        timeslot: '',
        email: '',
        phone: '',
        subject: '',
        instagram: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        // Static for now: nothing is sent to the server yet.
        setSubmitted(true);
    };

    return (
        <GuestLayout>
            <Head title="Podcast Sign Up" />

            <h1 className="text-lg font-semibold text-gray-900">
                Book a podcast slot
            </h1>
            <p className="mt-1 text-sm text-gray-600">
                Pick a time on Saturday 24th or Sunday 25th October 2026.
            </p>

            {submitted ? (
                <div className="mt-6 rounded-md bg-green-50 p-4 text-sm text-green-800">
                    Thanks, {data.name}! Your request has been noted.
                </div>
            ) : (
                <form onSubmit={submit} className="mt-6">
                    <div>
                        <InputLabel htmlFor="name" value="Name" />

                        <TextInput
                            id="name"
                            name="name"
                            value={data.name}
                            className="mt-1 block w-full"
                            autoComplete="name"
                            isFocused={true}
                            onChange={(e) => setData('name', e.target.value)}
                            required
                        />

                        <InputError message={errors.name} className="mt-2" />
                    </div>

                    <div className="mt-4">
                        <InputLabel htmlFor="timeslot" value="Timeslot" />

                        <select
                            id="timeslot"
                            name="timeslot"
                            value={data.timeslot}
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            onChange={(e) => setData('timeslot', e.target.value)}
                            required
                        >
                            <option value="" disabled>
                                Choose a time…
                            </option>
                            {DAYS.map((day) => (
                                <optgroup key={day.date} label={day.label}>
                                    {TIMES.map((time) => (
                                        <option
                                            key={time.value}
                                            value={`${day.date} ${time.value}`}
                                        >
                                            {time.label}
                                        </option>
                                    ))}
                                </optgroup>
                            ))}
                        </select>

                        <InputError message={errors.timeslot} className="mt-2" />
                    </div>

                    <div className="mt-4">
                        <InputLabel htmlFor="email" value="Email" />

                        <TextInput
                            id="email"
                            type="email"
                            name="email"
                            value={data.email}
                            className="mt-1 block w-full"
                            autoComplete="email"
                            onChange={(e) => setData('email', e.target.value)}
                            required
                        />

                        <InputError message={errors.email} className="mt-2" />
                    </div>

                    <div className="mt-4">
                        <InputLabel htmlFor="phone">{optional('Phone')}</InputLabel>

                        <TextInput
                            id="phone"
                            type="tel"
                            name="phone"
                            value={data.phone}
                            className="mt-1 block w-full"
                            autoComplete="tel"
                            onChange={(e) => setData('phone', e.target.value)}
                        />

                        <InputError message={errors.phone} className="mt-2" />
                    </div>

                    <div className="mt-4">
                        <InputLabel htmlFor="subject">
                            {optional('Podcast subject')}
                        </InputLabel>

                        <TextInput
                            id="subject"
                            name="subject"
                            value={data.subject}
                            className="mt-1 block w-full"
                            onChange={(e) => setData('subject', e.target.value)}
                        />

                        <InputError message={errors.subject} className="mt-2" />
                    </div>

                    <div className="mt-4">
                        <InputLabel htmlFor="instagram">
                            {optional('Instagram handle')}
                        </InputLabel>

                        <div className="mt-1 flex rounded-md shadow-sm">
                            <span className="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500">
                                @
                            </span>
                            <TextInput
                                id="instagram"
                                name="instagram"
                                value={data.instagram}
                                className="block w-full rounded-l-none shadow-none"
                                onChange={(e) =>
                                    setData(
                                        'instagram',
                                        e.target.value.replace(/^@/, ''),
                                    )
                                }
                            />
                        </div>

                        <InputError message={errors.instagram} className="mt-2" />
                    </div>

                    <div className="mt-6 flex justify-end">
                        <PrimaryButton>Book slot</PrimaryButton>
                    </div>
                </form>
            )}
        </GuestLayout>
    );
}
