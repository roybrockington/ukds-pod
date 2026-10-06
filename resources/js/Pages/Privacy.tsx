import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link } from '@inertiajs/react';
import { PropsWithChildren } from 'react';

const ORGANISATION = 'Sound Service U.K. Ltd';
const CONTACT_EMAIL = 'sales@soundservice.uk';

const LAST_UPDATED = '6 October 2026';
const DELETION_DATE = '24 November 2026';

function Placeholder({ children }: PropsWithChildren) {
    return children && String(children).startsWith('[') ? (
        <mark className="rounded bg-yellow-200 px-1">{children}</mark>
    ) : (
        <>{children}</>
    );
}

function Section({ title, children }: PropsWithChildren<{ title: string }>) {
    return (
        <section className="mt-6">
            <h2 className="text-base font-semibold text-gray-900">{title}</h2>
            <div className="mt-2 space-y-3">{children}</div>
        </section>
    );
}

function List({ children }: PropsWithChildren) {
    return <ul className="list-disc space-y-1 pl-5">{children}</ul>;
}

export default function Privacy() {
    const organisation = <Placeholder>{ORGANISATION}</Placeholder>;
    const contact = <Placeholder>{CONTACT_EMAIL}</Placeholder>;

    return (
        <GuestLayout wide>
            <Head title="Privacy Policy" />

            <div className="py-2 text-sm leading-6 text-gray-700">
                <h1 className="text-lg font-semibold text-gray-900">
                    Privacy policy
                </h1>
                <p className="mt-1 text-gray-500">
                    Last updated {LAST_UPDATED}
                </p>

                <p className="mt-4">
                    This policy explains how {organisation} handles your
                    personal information when you book a slot at the Audix x
                    Zoom Podcast Bar on Saturday 24th and Sunday 25th October
                    2026. In short: we only collect what we need to run your
                    booking, and we delete it all after the event.
                </p>

                <Section title="What we collect">
                    <p>When you request a booking, we collect:</p>
                    <List>
                        <li>your name and email address</li>
                        <li>the timeslot you choose</li>
                        <li>
                            if you choose to give them, your phone number,
                            podcast subject and Instagram handle
                        </li>
                    </List>
                    <p>
                        When you use the site, we also briefly process your IP
                        address and browser details to keep the site secure and
                        working (see below).
                    </p>
                </Section>

                <Section title="How we use it">
                    <p>We use your information only to:</p>
                    <List>
                        <li>review and confirm or decline your booking</li>
                        <li>
                            email you when your booking is confirmed, and
                            contact you about your slot if we need to
                        </li>
                        <li>run your podcast session on the day</li>
                        <li>protect the booking form from spam and abuse</li>
                    </List>
                    <p>
                        We will not use your information for marketing, and we
                        will never sell it.
                    </p>
                    <p>
                        Under UK data protection law, we process your booking
                        details because you have asked us to book you a slot,
                        and we process security information because we have a
                        legitimate interest in protecting the site.
                    </p>
                </Section>

                <Section title="Who we share it with">
                    <p>
                        Your booking details are seen only by the event team. We
                        use a small number of service providers to run the site:
                    </p>
                    <List>
                        <li>
                            <strong>Mailgun</strong> (EU region) sends our
                            booking emails, so it handles your name, email
                            address and booking details.
                        </li>
                        <li>
                            <strong>Cloudflare Turnstile</strong> checks that
                            the form is being used by a person rather than a
                            bot. It processes your IP address and browser
                            information. See{' '}
                            <a
                                href="https://www.cloudflare.com/turnstile-privacy-policy/"
                                className="underline hover:text-gray-900"
                                target="_blank"
                                rel="noreferrer"
                            >
                                Cloudflare's Turnstile privacy addendum
                            </a>
                            .
                        </li>
                        <li>
                            <strong>Bunny Fonts</strong> serves the font used on
                            this site, which means your browser connects to its
                            servers.
                        </li>
                    </List>
                </Section>

                <Section title="Cookies">
                    <p>
                        We only use cookies that are strictly necessary for the
                        site to work: a session cookie and a security token that
                        protects the form from cross-site request forgery. Both
                        expire after two hours of inactivity. We don't use
                        analytics, advertising or tracking cookies.
                    </p>
                </Section>

                <Section title="How long we keep it">
                    <p>
                        <strong>
                            All booking information, including our copies of
                            booking emails, will be permanently deleted after
                            the event, by {DELETION_DATE} at the latest.
                        </strong>{' '}
                        If you decline or cancel your booking before then, we
                        delete it straight away.
                    </p>
                    <p>
                        Session records containing your IP address and browser
                        details are deleted automatically shortly after your
                        session expires.
                    </p>
                </Section>

                <Section title="Your rights">
                    <p>
                        You can ask us to give you a copy of your information,
                        correct it, or delete it at any time before the event.
                        Deleting your information will cancel your booking. To
                        make a request, email us at {contact}.
                    </p>
                    <p>
                        If you're unhappy with how we've handled your
                        information, you can complain to the Information
                        Commissioner's Office at{' '}
                        <a
                            href="https://ico.org.uk/make-a-complaint/"
                            className="underline hover:text-gray-900"
                            target="_blank"
                            rel="noreferrer"
                        >
                            ico.org.uk
                        </a>
                        .
                    </p>
                </Section>

                <Section title="Contact">
                    <p>
                        This site is run by {organisation}. Questions about this
                        policy can be sent to {contact}.
                    </p>
                </Section>

                <div className="mt-8">
                    <Link
                        href={route('home')}
                        className="text-gray-600 underline hover:text-gray-900"
                    >
                        Back to the booking form
                    </Link>
                </div>
            </div>
        </GuestLayout>
    );
}
