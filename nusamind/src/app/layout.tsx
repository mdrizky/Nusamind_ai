import type { Metadata } from "next"
import { Inter, Poppins } from "next/font/google"
import "./globals.css"

const inter = Inter({ subsets: ["latin"] })
const poppins = Poppins({
  subsets: ["latin"],
  weight: ["300", "400", "500", "600", "700"],
  variable: "--font-poppins",
})

export const metadata: Metadata = {
  title: "Nusamind AI",
  description: "Asisten AI Digital All-in-One untuk UMKM Indonesia",
}

export default function RootLayout({
  children,
}: Readonly<{ children: React.ReactNode }>) {
  return (
    <html lang="id">
      <head>
        <meta name="dicoding:email" content="mdrizky240708@gmail.com" />
      </head>
      <body className={`${inter.className} ${poppins.variable}`}>
        {children}
      </body>
    </html>
  )
}
