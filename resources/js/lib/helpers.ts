export function label(text?: string) {
    if (!text) return;
    // slova, která se mají vždy psát malými písmeny
    const exclude = [
        'to',
        'and',
        'or',
        'of',
        'from',
        'with',
        'by',
        'in',
        'at',
        'for',
        'on',
        'off',
        'over',
        'under',
        'between',
        'among',
        'through',
        'during',
        'before',
        'after',
        'above',
        'below',
        'around',
        'near',
        'beyond',
        'within',
        'without',
        'against',
        'towards',
        'upon',
        'about',
        'across',
        'behind',
        'beside',
        'beneath',
        'into',
        'onto',
        'out',
        'up',
        'down',
        'off',
        'along',
        'past',
        'since',
        'until',
        'via',
        'a',
        'the',
        'an',
        'some',
        'any',
    ];

    // uplne ignorované slova
    const ignore = ['VM', 'PDU', 'SSD', 'HDD', 'CPU', 'RAM', 'GPU'];

    // emaily převedené na jména pouze
    if (text.includes('@')) {
        const [name, domain] = text.split('@');
        text = name;
    }

    return text
        .replace(/[-._]/g, ' ')
        .split(' ')
        .map((word, index, words) =>
            ignore.includes(word)
                ? word
                : exclude.includes(word.toLowerCase()) && index !== 0 && index !== words.length - 1
                  ? word.toLowerCase()
                  : word.charAt(0).toUpperCase() + word.slice(1).toLowerCase(),
        )
        .join(' ');
}


export function statusColor(status: string) {
    switch (status) {
        case 'pending':
            return 'bg-yellow-500/20 text-yellow-500 hover:bg-yellow-500/30';
        case 'approved':
            return 'bg-green-500/20 text-green-500 hover:bg-green-500/30';
        case 'rejected':
            return 'bg-red-500/20 text-red-500 hover:bg-red-500/30';
        default:
            return 'bg-gray-500/20 text-gray-500 hover:bg-gray-500/30';
    }
}
