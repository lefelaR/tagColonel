import { io, type Socket } from 'socket.io-client'

let socket: Socket | null = null
let currentUrl = ''

// One socket for the dashboard. Composables subscribe; they do not open their own connections.
export function courseSocket(url: string): Socket {
  if (socket && currentUrl === url) return socket

  socket?.close()
  currentUrl = url
  socket = io(url, { autoConnect: true })
  return socket
}
