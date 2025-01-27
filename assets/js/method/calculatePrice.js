export function calculatePrice(bookPrice, time) {
    bookPrice = parseInt(bookPrice, 10);
    let price = null;

    if (time === '1 an') {
        price = (Math.ceil(bookPrice / 2) > 600) ? Math.ceil(bookPrice / 2) : 600;
    } else if (time === '3 mois') {
        price = (Math.ceil((bookPrice + (bookPrice / 4)) / 4 + 200) > 500) ? Math.ceil((bookPrice + (bookPrice / 4)) / 4 + 200) : 500;
    } else if (time === '1 mois') {
        price = (Math.ceil(((bookPrice + (bookPrice / 4)) / 4 + 200) / 3 + 150) > 250) ? Math.ceil(((bookPrice + (bookPrice / 4)) / 4 + 200) / 3 + 150) : 250;
    } else if (time === '1 semaine') {
        price = (Math.ceil(((bookPrice + (bookPrice / 4)) / 4 + 200) / 3 + 150 / 4 + 100) > 150) ? Math.ceil(((bookPrice + (bookPrice / 4)) / 4 + 200) / 3 + 150 / 4 + 100) : 150;
    } else {
        price = (Math.ceil(((((bookPrice + (bookPrice / 4)) / 4 + 200) / 3 + 150) / 4 + 100) / 7 + 50) > 100) ? Math.ceil(((((bookPrice + (bookPrice / 4)) / 4 + 200) / 3 + 150) / 4 + 100) / 7 + 50) : 100;
    }
    return price;
}

