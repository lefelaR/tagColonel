export type PaceStatus = 'on_pace' | 'slow' | 'very_slow'

export interface LiveCart {
  cartId: number
  latitude: number
  longitude: number
  speed: number
  hole: number | null
  paceStatus: PaceStatus | null
}

export interface PaceAlert {
  id: number | null
  courseId: number
  cartId: number | null
  paceStatus: PaceStatus | null
  message: string
}

export interface CartUpdate {
  cart_id: number
  course_id: number
  lat: number
  lng: number
  speed: number
  hole?: number | null
  pace_status?: PaceStatus | null
}

export interface MarshalMessage {
  course_id: number
  cart_id?: number | null
  pace_status?: PaceStatus | null
  message: string
}
