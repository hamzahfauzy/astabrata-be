import view from '../core/view.js'

export default class AuthController {

    state = {
        email: '',
        password: '',
        btnLoginLabel: 'Sign In'
    }

    async index(ctx){
        if(!ctx.state)
        {
            ctx.state = this.state
        }

        ctx.onMounted(() => {
            ctx.on('#loginForm', 'submit', async e => {
                e.preventDefault()

                document.querySelector('.btn-login').innerHTML = 'Processing...'

                const login = await this.doLogin(ctx)

                if(login)
                {
                    document.querySelector('.btn-login').innerHTML = 'Login Success'
                    ctx.redirect('/')
                }
                else
                {
                    document.querySelector('.btn-login').innerHTML = 'Login Failed'
                    setTimeout(e => {
                        document.querySelector('.btn-login').innerHTML = 'Sign In'
                    }, 1000)
                }

            })
        })
        return await view.render('auth/login', ctx.state)
    }

    async doLogin(ctx)
    {
        const email = document.querySelector('input[name=email]').value
        const password = document.querySelector('input[name=password]').value
        ctx.state.email = email
        ctx.state.password = password

        try {
            
            const response = await ctx.http.post('/login', {
                email, password
            })

            if(response.data.token)
            {
                localStorage.setItem('token', response.data.token)
                return true
            }

    
        } catch (error) {
            
        }

        return false

    }

    async forgetPassword(ctx){
        ctx.onMounted(() => {
            ctx.on('#forgotForm', 'submit', e => {
                e.preventDefault()

                ctx.redirect('/login')
            })
        })

        return await view.render('auth/forgot-password')
    }

}