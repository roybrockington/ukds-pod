import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, useForm } from '@inertiajs/react';
import { Turnstile, TurnstileInstance } from '@marsidev/react-turnstile';
import { FormEventHandler, useRef } from 'react';

type Day = {
    label: string;
    slots: { value: string; label: string; booked: boolean }[];
};

const optional = (label: string) => (
    <>
        {label} <span className="font-normal text-gray-500">(optional)</span>
    </>
);

export default function SignUp({
    days,
    turnstileSiteKey,
}: {
    days: Day[];
    turnstileSiteKey: string;
}) {
    const turnstile = useRef<TurnstileInstance>(null);

    const { data, setData, post, processing, errors } = useForm({
        name: '',
        timeslot: '',
        email: '',
        phone: '',
        subject: '',
        instagram: '',
        turnstile: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('bookings.store'), {
            // Turnstile tokens are single-use, so get a fresh one before retrying.
            onError: () => {
                setData('turnstile', '');
                turnstile.current?.reset();
            },
        });
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
                        {days.map((day) => (
                            <optgroup key={day.label} label={day.label}>
                                {day.slots.map((slot) => (
                                    <option
                                        key={slot.value}
                                        value={slot.value}
                                        disabled={slot.booked}
                                    >
                                        {slot.booked
                                            ? `${slot.label} (booked)`
                                            : slot.label}
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

                <div className="mt-6">
                    <Turnstile
                        ref={turnstile}
                        siteKey={turnstileSiteKey}
                        options={{ theme: 'light', size: 'flexible' }}
                        onSuccess={(token) => setData('turnstile', token)}
                        onExpire={() => setData('turnstile', '')}
                        onError={() => setData('turnstile', '')}
                    />

                    <InputError message={errors.turnstile} className="mt-2" />
                </div>

                <div className="mt-6 flex justify-end">
                    <PrimaryButton disabled={processing || !data.turnstile}>
                        Book slot
                    </PrimaryButton>
                </div>
            </form>
        </GuestLayout>
    );
}
