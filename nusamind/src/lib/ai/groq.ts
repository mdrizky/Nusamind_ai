import Groq from 'groq-sdk'

const groq = new Groq({ apiKey: process.env.GROQ_API_KEY })

export type AiFeature =
  | 'finance' | 'marketing' | 'insight' | 'reply' | 'stock'
  | 'campaign' | 'loyal' | 'price' | 'catalog' | 'global' | 'coach'

export async function callGroq(systemPrompt: string, userMessage: string) {
  const completion = await groq.chat.completions.create({
    model: 'llama-3.3-70b-versatile',
    messages: [
      { role: 'system', content: systemPrompt },
      { role: 'user', content: userMessage },
    ],
    temperature: 0.7,
    max_tokens: 2000,
  })
  return completion.choices[0]?.message?.content || ''
}

export async function parseJsonResponse<T>(response: string): Promise<T> {
  const cleaned = response
    .replace(/```json\s*/g, '')
    .replace(/```\s*/g, '')
    .trim()
  return JSON.parse(cleaned) as T
}
