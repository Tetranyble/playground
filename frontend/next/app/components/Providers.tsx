'use client'

import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { SessionProvider } from "next-auth/react";
import { ReactNode, FC } from "react";

type Props = {
    children: ReactNode
}
const queryClient = new QueryClient()

export const Providers:FC<Props> = ({children}) => {
    return ( 
        <QueryClientProvider client={queryClient}>
            <SessionProvider>
            {children}
        </SessionProvider>
        </QueryClientProvider>
        
    );
}