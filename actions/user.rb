# encoding=utf-8

get '/gateway' do
  session_end!
  redirect to '/'
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

get '/login' do
  redirect to '/' if session?
  erb :auth
end

get '/me' do
  session!
  @user = User.get(session[:id]) or not_found
  erb :me
end

post '/me' do
  session!
  if params[:passwd]
    if params[:passwd] == params[:passwd2]
      if User.get(session[:id]).update passwd: hashpasswd(params[:passwd])
        alert '변경되었습니다.'
      else
        error 400, alert('변경 실패!')
      end
    else
      error 400, alert('암호 이상해!')
    end
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
