export interface User {
  id: number
  name: string
  email: string
  role: 'admin' | 'user'
  status: 'active' | 'suspended'
  created_at: string
}

export interface Business {
  id: number
  user_id: number
  category_id: number | null
  business_name: string
  city: string
  description: string | null
  brand_tone: string
  open_hours: string | null
  shipping_info: string | null
  whatsapp_number: string | null
  payment_methods: string | null
}

export interface Category {
  id: number
  name: string
  icon: string
}

export interface Product {
  id: number
  business_id: number
  name: string
  price: number
  stock: number
  description: string | null
  cost_estimate: number | null
  min_stock_alert: number
  unit: string
  image_url: string | null
  is_active: boolean
  tags: string | null
}

export interface Transaction {
  id: number
  user_id: number
  product_id: number | null
  type: 'pemasukan' | 'pengeluaran'
  item_name: string
  quantity: number | null
  amount: number
  source: string
  raw_input: string | null
  transaction_date: string
}

export interface ContentGeneration {
  id: number
  user_id: number
  product_id: number | null
  platform: string
  content_type: string
  caption_text: string | null
  hashtags: string | null
  style: string | null
  is_used: boolean
  created_at: string
}

export interface BusinessInsight {
  id: number
  user_id: number
  period_start: string | null
  period_end: string | null
  narrative_text: string | null
  summary_data: string | null
  created_at: string
}

export interface BusinessFaq {
  id: number
  business_id: number
  question: string
  answer: string
  category: string | null
  created_at: string
}

export interface CustomerReply {
  id: number
  business_id: number
  customer_message: string
  intent: string | null
  tone: string | null
  generated_reply: string
  is_saved: boolean
  created_at: string
}

export interface Customer {
  id: number
  business_id: number
  name: string
  phone: string | null
  address: string | null
  notes: string | null
  total_orders: number
  total_spent: number
  last_order_date: string | null
  segment: 'new' | 'regular' | 'vip'
  created_at: string
}

export interface CampaignPlan {
  id: number
  business_id: number
  campaign_name: string | null
  campaign_goal: string
  target_product_id: number | null
  plan_result: string | null
  caption: string | null
  broadcast_message: string | null
  start_date: string | null
  end_date: string | null
  is_active: boolean
  created_at: string
}

export interface StockMovement {
  id: number
  business_id: number
  product_id: number
  movement_type: 'in' | 'out' | 'adjustment'
  quantity: number
  reason: string | null
  transaction_id: number | null
  created_at: string
}

export interface HealthScore {
  id: number
  business_id: number
  total_score: number
  financial_score: number | null
  marketing_score: number | null
  sales_score: number | null
  customer_score: number | null
  stock_score: number | null
  breakdown_text: string | null
  recommendations: string | null
  scored_at: string
}

export interface AiUsageLog {
  id: number
  user_id: number
  feature: string
  tokens_used: number | null
  status: string
  created_at: string
}

export interface Notification {
  id: number
  user_id: number
  title: string
  body: string
  type: string | null
  is_read: boolean
  action_url: string | null
  created_at: string
}
