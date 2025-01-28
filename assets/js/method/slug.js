export function convertToSlug(text) {
    // Remplacer les espaces et les caractères spéciaux
    const slug = text.toLowerCase()
        .replace(/[^\w\s-]/g, '')  // Enlever les caractères spéciaux
        .replace(/\s+/g, '-')      // Remplacer les espaces par des tirets
        .replace(/-+/g, '-');      // Enlever les tirets doubles
    return slug;
}
