require 'dm-core'
require 'dm-aggregates'

module Yutarbbs::Model

  DataMapper::Logger.new $stdout, :debug
  
  DataMapper.setup :default, 'mysql://yutar@127.0.0.1/yutar'

  DataMapper.repository(:default).adapter.resource_naming_convention =
    DataMapper::NamingConventions::Resource::UnderscoredAndPluralizedWithoutModule

  class User
    include DataMapper::Resource

    property :id, Serial, field: 'uid'
    property :userid, String
    property :passwd, String
    property :name, String
    property :year, Integer
    property :phone, String
    property :email, String
    property :remark, Text
    property :updated_on, Date
  end

  class Article
    include DataMapper::Resource

    storage_names[:default] = 'threads'

    property :id, Serial, field: 'tid'
    property :fid, Integer
    property :cid, Integer
    property :subject, String
    property :message, Text
    property :created_at, DateTime
    property :hits, Integer
    property :attachment, String

    belongs_to :user, child_key: [ :uid ], parent_key: [ :id ]

    has n, :messages, 'Message', parent_key: [ :id ], child_key: [ :tid ]

    def self.latest forum_id
      first(forum_id, order: :created_at.desc)[0]
    end
  end

  class Message
    include DataMapper::Resource

    property :id, Serial, field: 'mid'
    property :message, Text
    property :created_at, DateTime

    belongs_to :user, child_key: [ :uid ], parent_key: [ :id ]
    belongs_to :article, child_key: [ :tid ]
  end

  DataMapper.finalize

end
