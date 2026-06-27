import { NextResponse } from 'next/server'
import { createServerSupabase } from '@/lib/supabase/server'
import { getAuthUser } from '@/lib/supabase/helpers'

export async function POST() {
  try {
    const supabase = await createServerSupabase()
    const authUser = await getAuthUser(supabase)
    if (!authUser) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })

    const { data: business } = await supabase
      .from('businesses')
      .select('id')
      .eq('user_id', authUser.localId)
      .single()

    if (!business) return NextResponse.json({ error: 'Business not found' }, { status: 404 })

    const businessId = business.id

    const thirtyDaysAgo = new Date()
    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30)
    const thirtyDaysAgoStr = thirtyDaysAgo.toISOString().split('T')[0]

    const { data: incomeData } = await supabase
      .from('transactions')
      .select('amount')
      .eq('user_id', authUser.localId)
      .eq('type', 'pemasukan')
      .gte('transaction_date', thirtyDaysAgoStr)

    const { data: expenseData } = await supabase
      .from('transactions')
      .select('amount')
      .eq('user_id', authUser.localId)
      .eq('type', 'pengeluaran')
      .gte('transaction_date', thirtyDaysAgoStr)

    const totalIncome = (incomeData || []).reduce((sum: number, t: any) => sum + (t.amount || 0), 0)
    const totalExpense = (expenseData || []).reduce((sum: number, t: any) => sum + (t.amount || 0), 0)
    const financialScore = totalExpense > 0
      ? Math.min(100, Math.round((totalIncome / totalExpense) * 100))
      : totalIncome > 0 ? 100 : 0

    const { count: contentCount } = await supabase
      .from('content_generations')
      .select('*', { count: 'exact', head: true })
      .eq('user_id', authUser.localId)
      .gte('created_at', thirtyDaysAgoStr)

    const marketingScore = Math.min(100, (contentCount || 0) * 20)

    const { count: transactionCount } = await supabase
      .from('transactions')
      .select('*', { count: 'exact', head: true })
      .eq('user_id', authUser.localId)
      .gte('transaction_date', thirtyDaysAgoStr)

    const salesScore = Math.min(100, (transactionCount || 0) * 10)

    const { count: customerCount } = await supabase
      .from('customers')
      .select('*', { count: 'exact', head: true })
      .eq('business_id', businessId)

    const customerScore = Math.min(100, (customerCount || 0) * 20)

    const { data: products } = await supabase
      .from('products')
      .select('stock, min_stock_alert')
      .eq('business_id', businessId)

    let stockScore = 0
    if (products && products.length > 0) {
      const healthyProducts = products.filter(
        (p: any) => p.stock >= p.min_stock_alert
      ).length
      stockScore = Math.round((healthyProducts / products.length) * 100)
    }

    const totalScore = Math.round(
      (financialScore + marketingScore + salesScore + customerScore + stockScore) / 5
    )

    const { data: saved, error } = await supabase.from('health_scores').insert({
      business_id: businessId,
      total_score: totalScore,
      financial_score: financialScore,
      marketing_score: marketingScore,
      sales_score: salesScore,
      customer_score: customerScore,
      stock_score: stockScore,
    }).select().single()

    if (error) return NextResponse.json({ error: error.message }, { status: 400 })

    return NextResponse.json({
      data: saved,
      breakdown: {
        financial_score: financialScore,
        marketing_score: marketingScore,
        sales_score: salesScore,
        customer_score: customerScore,
        stock_score: stockScore,
        total_score: totalScore,
      },
    })
  } catch (error) {
    return NextResponse.json({ error: 'Internal Server Error' }, { status: 500 })
  }
}
