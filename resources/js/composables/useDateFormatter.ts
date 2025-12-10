/**
 * Vue composable that provides a UTC-aware date formatter.
 *
 * Use Intl.DateTimeFormat with en-US locale and locked UTC timezone
 * to ensure consistency with Laravel's storage format.
 *
 * @param customOptions - Optional overrides for formatting (e.g., omit seconds)
 * @return Object with 'formatDate' function
 */
type DateFormatterOptions = Omit<Intl.DateTimeFormatOptions, 'timeZone'>;

const DEFAULT_OPTIONS: DateFormatterOptions = {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hourCycle: 'h23',
};

export function useDateFormatter(
    customOptions: DateFormatterOptions = {}
) {
    // Merge custom options with defaults
    const options = { ...DEFAULT_OPTIONS, ...customOptions };

    const formatter = new Intl.DateTimeFormat('en-US', {
        timeZone: 'UTC', // Locked to UTC (Laravel standard)
        ...options,
    });

    /**
     * Format a date string or Date object as a UTC timestamp.
     *
     * @param dateString - The date to format (ISO string, Date object, or nullish)
     * @return Formatted string or placeholder if invalid
     */
    const formatDate = (dateString: string | Date | null | undefined): string => {
        // Handle null/undefined
        if (!dateString) {
            return '--';
        }

        const date = new Date(dateString);

        if (isNaN(date.getTime())) {
            return 'Invalid Date';
        }

        return formatter.format(date);
    };

    return { formatDate };
}
