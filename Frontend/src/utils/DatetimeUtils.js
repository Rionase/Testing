export function formatDate(dateInput, locale = 'id-ID') {
    if (!dateInput) return '-'
    const date = new Date(dateInput)
    if (isNaN(date.getTime())) return '-'

    const day = date.getDate()
    const monthName = new Intl.DateTimeFormat(locale, { month: 'long' }).format(date)
    const year = date.getFullYear()

    const pad = (num) => String(num).padStart(2, '0')
    const hours = pad(date.getHours())
    const minutes = pad(date.getMinutes())
    const seconds = pad(date.getSeconds())

    return `${day} ${monthName} ${year} ${hours}:${minutes}:${seconds}`
}