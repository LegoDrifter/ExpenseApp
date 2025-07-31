/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors:{
                customGray:'#f6f6f6',
                slateGray:'#3B5555',
                slateLight:'#94b4b1',
                whiteSmoke:'#f5f5f4',
            },
            fontFamily:{
                anton:['Anton','sans-serif'],
                oswald: ['Oswald', 'sans-serif'],

            }
        },
    },
    plugins: [],
}

