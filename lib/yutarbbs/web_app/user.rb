# encoding=utf-8

module Yutarbbs
  class WebApp
    get '/gateway' do
      session_end!
      redirect to '/'
    end

    def hashpasswd text
      Digest::MD5.hexdigest "gkfd#{text}gkfd"
    end

    post '/gateway' do
      user = User.first(
        userid: params[:userid],
        passwd: hashpasswd(params[:passwd]))
      outdated = Time.now - user.updated_on.to_time > 90 * 24 * 60 * 60 # 3 months
      if user
        session_start!
        %w/id year name userid/.each do |k|
          session[k.to_sym] = user[k]
        end
        response.set_cookie :keeplogin, value: '1',
          expires: params[:keeplogin] ? Time.now + 365 * 86400 : Time.at(0)
        redirect to '/me' if outdated
      else
        session_end!
      end
      redirect back
    end

    get '/me' do
      session!
      @user = User.get(session[:id]) or halt 404
      erb :me
    end

    post '/me' do
      session!
      if params[:passwd]
        halt 400, alert('응?') if params[:passwd].empty?
        halt 400, alert('응?') if params[:passwd] != params[:passwd2]
        changed = User.get(session[:id]).update(
          passwd: hashpasswd(params[:passwd])
        )
        halt 500, alert('변경 실패!') unless changed
        alert '변경되었습니다.'
      elsif params[:phone]
        User.get(session[:id]).update(
          email: params[:email],
          phone: params[:phone],
          remark: params[:remark],
          updated_on: Time.now)
        redirect back
      end
    end

    get '/users' do
      session!
      @users = User.all :order => [ :year.desc, :name ]
      erb :users
    end
  end
end
